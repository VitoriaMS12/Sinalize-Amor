<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../model/conexao.php";

try {
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(405);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Método não permitido."
        ]);

        exit;
    }

// Recupera o cabeçalho Authorization enviado pelo aplicativo.
$cabecalhoAutorizacao = $_SERVER["HTTP_AUTHORIZATION"]
    ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"]
    ?? "";

// Se o Apache não disponibilizar o cabeçalho em $_SERVER,
// tenta recuperá-lo usando getallheaders().
if (
    $cabecalhoAutorizacao === ""
    && function_exists("getallheaders")
) {
    $cabecalhos = getallheaders();

    foreach ($cabecalhos as $nome => $valor) {
        if (strcasecmp($nome, "Authorization") === 0) {
            $cabecalhoAutorizacao = $valor;
            break;
        }
    }
}

// Aceita o token com ou sem o prefixo "Bearer".
if (
    preg_match(
        '/^Bearer\s+(.+)$/i',
        trim($cabecalhoAutorizacao),
        $correspondencia
    )
) {
    $token = trim($correspondencia[1]);
} else {
    $token = trim($cabecalhoAutorizacao);
}

// Verifica se o token foi enviado.
if ($token === "") {
    http_response_code(401);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Token de autenticação não informado."
    ]);

    exit;
}

// Calcula o hash do token para consultar o banco de dados.
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

// Verifica se o token existe e continua válido.
if (!$autenticacao) {
    http_response_code(401);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Sessão inválida ou expirada. Entre novamente."
    ]);

    exit;
}

// Identifica o usuário autenticado.
$idUsuario = (int) $autenticacao["id_usuario"];



    /*
     * Esta primeira versão somente consulta publicações públicas.
     * Ainda não permite publicar, curtir, comentar ou excluir.
     */

$sql = $pdo->prepare("
    SELECT
        p.id_post,
        p.id_usuario,
        p.texto,
        p.privacidade,
        p.data_postagem,
        p.editado,
        u.nome,
        u.sobrenome,
        u.username,
        u.foto_perfil,

        (
            SELECT COUNT(*)
            FROM curtida c
            WHERE c.id_post = p.id_post
        ) AS total_curtidas,

        (
            SELECT COUNT(*)
            FROM comentario cm
            WHERE cm.id_post = p.id_post
        ) AS total_comentarios,

        EXISTS (
            SELECT 1
            FROM curtida c2
            WHERE c2.id_post = p.id_post
              AND c2.id_usuario = ?
        ) AS curtido_pelo_usuario

    FROM post p

    INNER JOIN usuario u
        ON u.id_usuario = p.id_usuario

    WHERE
        p.ativo = 1
        AND p.privacidade = 'publico'
        AND u.ativo = 1

    ORDER BY p.data_postagem DESC
    LIMIT 100
");


$sql->execute([$idUsuario]);

    $publicacoes = $sql->fetchAll(PDO::FETCH_ASSOC);

    foreach ($publicacoes as &$publicacao) {

        $idPost = (int) $publicacao["id_post"];

        $publicacao["curtido_pelo_usuario"] =
            (bool) $publicacao["curtido_pelo_usuario"];

        $sqlMidias = $pdo->prepare("
            SELECT
                id_midia,
                arquivo,
                tipo,
                ordem
            FROM post_midia
            WHERE id_post = ?
            ORDER BY ordem ASC, id_midia ASC
        ");

        $sqlMidias->execute([$idPost]);

        $publicacao["midias"] = $sqlMidias->fetchAll(PDO::FETCH_ASSOC);

        $sqlCurtidas = $pdo->prepare("
            SELECT COUNT(*)
            FROM curtida
            WHERE id_post = ?
        ");

        $sqlCurtidas->execute([$idPost]);

        $publicacao["total_curtidas"] = (int) $sqlCurtidas->fetchColumn();

        $sqlComentarios = $pdo->prepare("
            SELECT COUNT(*)
            FROM comentario
            WHERE id_post = ?
              AND ativo = 1
        ");

        $sqlComentarios->execute([$idPost]);

        $publicacao["total_comentarios"] =
            (int) $sqlComentarios->fetchColumn();

        $publicacao["id_post"] = $idPost;
        $publicacao["id_usuario"] = (int) $publicacao["id_usuario"];
        $publicacao["editado"] = (int) $publicacao["editado"];
    }

    unset($publicacao);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Publicações carregadas com sucesso.",
        "publicacoes" => $publicacoes
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    error_log("Erro em comunidadeApp.php: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível carregar as publicações."
    ], JSON_UNESCAPED_UNICODE);
}