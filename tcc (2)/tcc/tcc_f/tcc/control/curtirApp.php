<?php
require_once "../model/conexao.php";

header("Content-Type: application/json; charset=utf-8");

function responder($sucesso, $mensagem, $codigo = 200, $dados = []) {
    http_response_code($codigo);

    echo json_encode(
        array_merge([
            "sucesso" => $sucesso,
            "mensagem" => $mensagem
        ], $dados),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(false, "Método não permitido.", 405);
}

// Recebe o token enviado pelo aplicativo.
$token = trim($_POST["token"] ?? "");

// Recebe o ID da publicação.
$idPost = filter_var(
    $_POST["id_post"] ?? null,
    FILTER_VALIDATE_INT
);

if ($token === "" || !$idPost || $idPost <= 0) {
    responder(false, "Token ou publicação inválidos.", 400);
}

try {
    // Valida o token e identifica o usuário autenticado.
    $tokenHash = hash("sha256", $token);

    $sqlToken = $pdo->prepare("
        SELECT id_usuario
        FROM token_autenticacao
        WHERE token_hash = ?
          AND ativo = 1
          AND data_expiracao > NOW()
        LIMIT 1
    ");

    $sqlToken->execute([$tokenHash]);
    $autenticacao = $sqlToken->fetch(PDO::FETCH_ASSOC);

    if (!$autenticacao) {
        responder(false, "Sessão inválida ou expirada. Entre novamente.", 401);
    }

    $idUsuario = (int) $autenticacao["id_usuario"];

    // Confere se a publicação existe e está disponível.
    $sqlPost = $pdo->prepare("
        SELECT id_post
        FROM post
        WHERE id_post = ?
          AND ativo = 1
          AND privacidade = 'publico'
        LIMIT 1
    ");

    $sqlPost->execute([$idPost]);

    if (!$sqlPost->fetch(PDO::FETCH_ASSOC)) {
        responder(false, "Publicação não encontrada.", 404);
    }

    // Verifica se o usuário já curtiu a publicação.
    $sqlCurtida = $pdo->prepare("
        SELECT id_curtida
        FROM curtida
        WHERE id_post = ? AND id_usuario = ?
        LIMIT 1
    ");

    $sqlCurtida->execute([$idPost, $idUsuario]);
    $curtida = $sqlCurtida->fetch(PDO::FETCH_ASSOC);

    if ($curtida) {
        // Se já curtiu, remove a curtida.
        $sqlRemover = $pdo->prepare("
            DELETE FROM curtida
            WHERE id_post = ? AND id_usuario = ?
        ");

        $sqlRemover->execute([$idPost, $idUsuario]);

        $curtido = false;
        $mensagem = "Curtida removida.";
    } else {
        // Se ainda não curtiu, registra a curtida.
        $sqlAdicionar = $pdo->prepare("
            INSERT INTO curtida (id_post, id_usuario)
            VALUES (?, ?)
        ");

        $sqlAdicionar->execute([$idPost, $idUsuario]);

        $curtido = true;
        $mensagem = "Publicação curtida.";
    }

    // Conta as curtidas atuais da publicação.
    $sqlTotal = $pdo->prepare("
        SELECT COUNT(*)
        FROM curtida
        WHERE id_post = ?
    ");

    $sqlTotal->execute([$idPost]);
    $totalCurtidas = (int) $sqlTotal->fetchColumn();

    responder(true, $mensagem, 200, [
        "curtido" => $curtido,
        "total_curtidas" => $totalCurtidas
    ]);

} catch (Throwable $e) {
    error_log("Erro em curtirApp.php: " . $e->getMessage());

    responder(false, "Não foi possível processar a curtida.", 500);
}