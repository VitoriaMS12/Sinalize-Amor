<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../model/conexao.php";

try {
    // Aceita apenas GET e POST.
    $metodo = $_SERVER["REQUEST_METHOD"];

    if (!in_array($metodo, ["GET", "POST"])) {
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

    $token = preg_replace('/^Bearer\s+/i', '', trim($cabecalho));

    if ($token === "") {
        http_response_code(401);
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Token não informado."
        ]);
        exit;
    }

    // Valida o token.
    $hash = hash("sha256", $token);

    $consultaToken = $pdo->prepare("
        SELECT id_usuario
        FROM token_autenticacao
        WHERE token_hash = ?
          AND ativo = 1
          AND data_expiracao > NOW()
        LIMIT 1
    ");

    $consultaToken->execute([$hash]);
    $sessao = $consultaToken->fetch(PDO::FETCH_ASSOC);

    if (!$sessao) {
        http_response_code(401);
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Sessão inválida ou expirada."
        ]);
        exit;
    }

    $idUsuario = (int) $sessao["id_usuario"];

    // LISTAR COMENTÁRIOS DE UMA PUBLICAÇÃO.
    if ($metodo === "GET") {
        $idPost = filter_input(INPUT_GET, "id_post", FILTER_VALIDATE_INT);

        if (!$idPost || $idPost <= 0) {
            http_response_code(400);
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Publicação inválida."
            ]);
            exit;
        }

        $sql = $pdo->prepare("
            SELECT
                c.id_comentario,
                c.id_post,
                c.id_usuario,
                c.comentario,
                c.editado,
                c.data_comentario,
                u.nome,
                u.sobrenome,
                u.username,
                u.foto_perfil
            FROM comentario c
            INNER JOIN usuario u ON u.id_usuario = c.id_usuario
            WHERE c.id_post = ?
              AND c.ativo = 1
              AND u.ativo = 1
            ORDER BY c.data_comentario ASC
        ");

        $sql->execute([$idPost]);
        $comentarios = $sql->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comentarios as &$comentario) {
            $comentario["id_comentario"] =
                (int) $comentario["id_comentario"];
            $comentario["id_post"] = (int) $comentario["id_post"];
            $comentario["id_usuario"] = (int) $comentario["id_usuario"];
            $comentario["editado"] = (int) $comentario["editado"];
        }
        unset($comentario);

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Comentários carregados.",
            "comentarios" => $comentarios
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    // PUBLICAR UM NOVO COMENTÁRIO.
    $idPost = filter_input(
        INPUT_POST,
        "id_post",
        FILTER_VALIDATE_INT
    );

    $texto = trim($_POST["comentario"] ?? "");

    if (!$idPost || $idPost <= 0 || $texto === "") {
        http_response_code(400);
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Informe a publicação e o comentário."
        ]);
        exit;
    }

    // Confirma que a publicação existe e está disponível.
    $verificarPost = $pdo->prepare("
        SELECT id_post
        FROM post
        WHERE id_post = ?
          AND ativo = 1
          AND privacidade = 'publico'
        LIMIT 1
    ");

    $verificarPost->execute([$idPost]);

    if (!$verificarPost->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(404);
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Publicação não encontrada."
        ]);
        exit;
    }

    $inserir = $pdo->prepare("
        INSERT INTO comentario
            (id_post, id_usuario, comentario)
        VALUES (?, ?, ?)
    ");

    $inserir->execute([$idPost, $idUsuario, $texto]);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Comentário publicado!",
        "id_comentario" => (int) $pdo->lastInsertId()
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    error_log("Erro comentarioApp.php: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao processar o comentário."
    ]);
}