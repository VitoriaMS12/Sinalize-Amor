<?php

require_once "../model/conexao.php";

header("Content-Type: application/json; charset=utf-8");

function responder($sucesso, $mensagem, $fotoPerfil = null) {
    echo json_encode([
        "sucesso" => $sucesso,
        "mensagem" => $mensagem,
        "foto_perfil" => $fotoPerfil
    ]);
    exit;
}

// Aceita somente requisições POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    responder(false, "Método não permitido.");
}

// Recupera o token enviado no cabeçalho Authorization.
$cabecalho = $_SERVER["HTTP_AUTHORIZATION"]
    ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"]
    ?? getallheaders()["Authorization"]
    ?? "";
if (preg_match('/^Bearer\s+(.+)$/i', $cabecalho, $matches)) {
    $token = trim($matches[1]);
} else {
    $token = "";
}

if ($token === "") {
    http_response_code(401);
    responder(false, "Token de autenticação não informado.");
}

try {
    // Calcula o hash do token recebido.
    $tokenHash = hash("sha256", $token);

    // Identifica o usuário por meio de um token ativo e válido.
    $sqlToken = $pdo->prepare("
        SELECT id_usuario
        FROM token_autenticacao
        WHERE token_hash = ?
          AND ativo = 1
          AND data_expiracao > NOW()
        LIMIT 1
    ");

    $sqlToken->execute([$tokenHash]);

    $idUsuario = $sqlToken->fetchColumn();

    if (!$idUsuario) {
        http_response_code(401);
        responder(false, "Token inválido ou expirado. Entre novamente.");
    }

    // Verifica se o arquivo foi enviado corretamente.
    if (
        !isset($_FILES["foto"]) ||
        $_FILES["foto"]["error"] !== UPLOAD_ERR_OK
    ) {
        responder(false, "Não foi possível receber a imagem.");
    }

    $arquivo = $_FILES["foto"];

    // Limite de 5 MB.
    if ($arquivo["size"] <= 0 || $arquivo["size"] > 5 * 1024 * 1024) {
        responder(false, "A imagem deve ter no máximo 5 MB.");
    }

    // Valida o formato real da imagem.
    $imagem = new finfo(FILEINFO_MIME_TYPE);
    $tipo = $imagem->file($arquivo["tmp_name"]);

    $tiposPermitidos = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($tiposPermitidos[$tipo])) {
        responder(false, "Formato não permitido. Use JPG, PNG ou WebP.");
    }

    // Confirma que o arquivo pode ser interpretado como imagem.
    if (@getimagesize($arquivo["tmp_name"]) === false) {
        responder(false, "O arquivo enviado não é uma imagem válida.");
    }

    $extensao = $tiposPermitidos[$tipo];

    // Nome aleatório para evitar colisões e nomes previsíveis.
    $nomeArquivo = bin2hex(random_bytes(16)) . "." . $extensao;

    // Pasta de destino.
    $pasta = "../uploads/perfils/";

    if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
        throw new RuntimeException("Não foi possível criar a pasta de imagens.");
    }

    $caminho = $pasta . $nomeArquivo;

    if (!move_uploaded_file($arquivo["tmp_name"], $caminho)) {
        throw new RuntimeException("Não foi possível salvar a imagem.");
    }

    // Atualiza a foto somente do usuário identificado pelo token.
    $sqlFoto = $pdo->prepare("
        UPDATE usuario
        SET foto_perfil = ?
        WHERE id_usuario = ?
    ");

    $sqlFoto->execute([
        $nomeArquivo,
        $idUsuario
    ]);

    // Confirma que a atualização foi aplicada ao usuário.
    if ($sqlFoto->rowCount() === 0) {
        $sqlConfirma = $pdo->prepare("
            SELECT foto_perfil
            FROM usuario
            WHERE id_usuario = ?
        ");

        $sqlConfirma->execute([$idUsuario]);
        $fotoAtual = $sqlConfirma->fetchColumn();

        if ($fotoAtual !== $nomeArquivo) {
            if (file_exists($caminho)) {
                unlink($caminho);
            }

            responder(false, "Não foi possível atualizar a foto do perfil.");
        }
    }

    responder(true, "Foto de perfil atualizada com sucesso.", $nomeArquivo);

} catch (Throwable $e) {
    error_log("Erro ao trocar foto pelo aplicativo: " . $e->getMessage());

    http_response_code(500);
    responder(false, "Não foi possível atualizar a foto. Tente novamente.");
}