<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../model/conexao.php";

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Método não permitido."
        ]);

        exit;
    }

    // Recupera o token de autenticação.
    $cabecalho = $_SERVER["HTTP_AUTHORIZATION"]
        ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"]
        ?? "";

    if ($cabecalho === "" && function_exists("getallheaders")) {
        foreach (getallheaders() as $nome => $valor) {
            if (strcasecmp($nome, "Authorization") === 0) {
                $cabecalho = $valor;
                break;
            }
        }
    }

    // Aceita Bearer ou token sem prefixo.
    if (preg_match('/^Bearer\s+(.+)$/i', trim($cabecalho), $m)) {
        $token = trim($m[1]);
    } else {
        $token = trim($cabecalho);
    }

    if ($token === "") {
        http_response_code(401);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Token não informado."
        ]);

        exit;
    }

    // Identifica o usuário autenticado.
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
        http_response_code(401);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Sessão inválida ou expirada."
        ]);

        exit;
    }

    $idUsuario = (int) $autenticacao["id_usuario"];
    $idPost = filter_input(INPUT_POST, "id_post", FILTER_VALIDATE_INT);

    if (!$idPost || $idPost <= 0) {
        http_response_code(400);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Publicação inválida."
        ]);

        exit;
    }

    // Só permite excluir uma publicação do próprio usuário.
    $sqlPost = $pdo->prepare("
        SELECT id_post
        FROM post
        WHERE id_post = ?
          AND id_usuario = ?
          AND ativo = 1
        LIMIT 1
    ");

    $sqlPost->execute([$idPost, $idUsuario]);

    if (!$sqlPost->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(403);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Publicação não encontrada ou você não tem permissão para excluí-la."
        ]);

        exit;
    }

    // Exclusão lógica: mantém os dados no banco, mas retira do feed.
    $sqlExcluir = $pdo->prepare("
        UPDATE post
        SET ativo = 0
        WHERE id_post = ?
          AND id_usuario = ?
    ");

    $sqlExcluir->execute([$idPost, $idUsuario]);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Publicação excluída com sucesso."
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    error_log("Erro em excluirPublicacaoApp.php: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível excluir a publicação."
    ], JSON_UNESCAPED_UNICODE);
}