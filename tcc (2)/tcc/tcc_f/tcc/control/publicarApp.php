<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../model/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método não permitido.'
    ]);

    exit;
}

// Obtém o token enviado pelo aplicativo.
$headers = function_exists('getallheaders') ? getallheaders() : [];

$autorizacao = $_SERVER['HTTP_AUTHORIZATION']
    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
    ?? $headers['Authorization']
    ?? $headers['authorization']
    ?? '';

$token = preg_replace('/^Bearer\s+/i', '', trim($autorizacao));

// Aceita também o token enviado no corpo da requisição.
if ($token === '') {
    $token = trim($_POST['token'] ?? '');
}

if ($token === '') {
    http_response_code(401);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Token não informado.'
    ]);

    exit;
}

// Valida o token e identifica o usuário autenticado.
try {

    $sqlUsuario = "
    SELECT id_usuario
    FROM token_autenticacao
    WHERE token_hash = :token_hash
      AND ativo = 1
      AND data_expiracao > NOW()
    LIMIT 1
";

    $tokenHash = hash('sha256', $token);
    $stmtUsuario = $pdo->prepare($sqlUsuario);
    $stmtUsuario->execute(['token_hash' => $tokenHash]);

    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Sessão inválida ou expirada. Faça login novamente.'
        ]);

        exit;
    }

    $idUsuario = (int) $usuario['id_usuario'];


// Obtém o texto e verifica se uma imagem foi enviada.
$texto = trim($_POST['texto'] ?? '');

$imagemEnviada = isset($_FILES['imagem'])
    && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE;

// Exige texto ou imagem.
if ($texto === '' && !$imagemEnviada) {
    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Escreva algo ou selecione uma imagem antes de publicar.'
    ]);

    exit;
}

 // Verifica se uma imagem foi enviada.
$arquivoImagem = null;
$caminhoImagem = null;

if ($imagemEnviada) {
    $imagem = $_FILES['imagem'];

    // Verifica se o upload terminou corretamente.
    if ($imagem['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Ocorreu um erro ao enviar a imagem.'
        ]);

        exit;
    }

    // Limita o tamanho da imagem a 5 MB.
    if ($imagem['size'] > 5 * 1024 * 1024) {
        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'A imagem deve ter no máximo 5 MB.'
        ]);

        exit;
    }

    // Verifica o tipo real do arquivo.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipoImagem = $finfo->file($imagem['tmp_name']);

    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($tiposPermitidos[$tipoImagem])) {
        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Formato inválido. Envie uma imagem JPG, PNG ou WEBP.'
        ]);

        exit;
    }

    // Prepara o nome e o destino do arquivo.
    $extensao = $tiposPermitidos[$tipoImagem];
    $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;

    $pastaPublicacoes = realpath(__DIR__ . '/../uploads');

    if ($pastaPublicacoes === false) {
        throw new RuntimeException('A pasta uploads não foi encontrada.');
    }

    $pastaPublicacoes .= DIRECTORY_SEPARATOR . 'publicacoes';

    if (!is_dir($pastaPublicacoes) &&
        !mkdir($pastaPublicacoes, 0755, true) &&
        !is_dir($pastaPublicacoes)) {
        throw new RuntimeException('Não foi possível criar a pasta de publicações.');
    }

    $caminhoImagem = $pastaPublicacoes . DIRECTORY_SEPARATOR . $nomeArquivo;
    $arquivoImagem = 'uploads/publicacoes/' . $nomeArquivo;
}

// Cria o post e registra a imagem na mesma transação.
$pdo->beginTransaction();

try {
    $sqlPost = "
        INSERT INTO post (id_usuario, texto, privacidade)
        VALUES (:id_usuario, :texto, 'publico')
    ";

    $stmtPost = $pdo->prepare($sqlPost);
    $stmtPost->execute([
        'id_usuario' => $idUsuario,
        'texto' => $texto
    ]);

    $idPost = (int) $pdo->lastInsertId();

    if ($imagemEnviada) {
        if (!move_uploaded_file($imagem['tmp_name'], $caminhoImagem)) {
            throw new RuntimeException('Não foi possível salvar a imagem no servidor.');
        }

        $sqlMidia = "
            INSERT INTO post_midia (id_post, arquivo, tipo, ordem)
            VALUES (:id_post, :arquivo, 'imagem', 1)
        ";

        $stmtMidia = $pdo->prepare($sqlMidia);
        $stmtMidia->execute([
            'id_post' => $idPost,
            'arquivo' => $arquivoImagem
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Publicação criada com sucesso!',
        'id_post' => $idPost
    ]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Remove o arquivo caso tenha sido salvo, mas a operação falhe.
    if ($caminhoImagem !== null && is_file($caminhoImagem)) {
        unlink($caminhoImagem);
    }

    throw $e;
}

} catch (Throwable $e) {
    error_log('Erro ao publicar na comunidade: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno: ' . $e->getMessage()
    ]);
}