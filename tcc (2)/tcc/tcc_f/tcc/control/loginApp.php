<?php

// Conecta com o banco de dados.
require_once "../model/conexao.php";

// Define que a resposta será em formato JSON.
header("Content-Type: application/json; charset=utf-8");

// Verifica se o aplicativo enviou os dados usando POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido."
    ]);

    exit;
}

// Recebe o e-mail e a senha enviados pelo aplicativo.
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";

// Verifica se os campos foram preenchidos.
if ($email === "" || $senha === "") {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Preencha todos os campos."
    ]);

    exit;
}

try {

    // Procura o usuário pelo e-mail.
    $sql = $pdo->prepare("
        SELECT
            id_usuario,
            nome,
            sobrenome,
            username,
            email,
            senha_hash,
            foto_perfil
        FROM usuario
        WHERE email = ?
        LIMIT 1
    ");

    $sql->execute([$email]);

    // Obtém os dados encontrados.
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe.
    if (!$usuario) {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "E-mail ou senha inválidos."
        ]);

        exit;
    }

    // Confere se a senha corresponde à senha cadastrada.
    if (!password_verify($senha, $usuario["senha_hash"])) {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "E-mail ou senha inválidos."
        ]);

        exit;
    }

    // Guarda o ID do usuário antes de remover a senha.
    $idUsuario = (int) $usuario["id_usuario"];

    // Remove o hash da senha dos dados enviados ao aplicativo.
    unset($usuario["senha_hash"]);

    // Gera um token aleatório e seguro.
    $token = bin2hex(random_bytes(32));

    // Salva somente o hash do token no banco de dados.
    $tokenHash = hash("sha256", $token);

    // Define a validade do token: 30 dias.
    $sqlToken = $pdo->prepare("
        INSERT INTO token_autenticacao (
            id_usuario,
            token_hash,
            data_expiracao,
            ativo
        )
        VALUES (
            ?,
            ?,
            DATE_ADD(NOW(), INTERVAL 30 DAY),
            1
        )
    ");

    $sqlToken->execute([
        $idUsuario,
        $tokenHash
    ]);

    // Retorna os dados do usuário e o token ao aplicativo.
    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Login realizado com sucesso.",
        "usuario" => $usuario,
        "token" => $token
    ]);

} catch (Throwable $e) {

    // Registra o erro técnico no servidor sem expor detalhes ao aplicativo.
    error_log("Erro no login do aplicativo: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível realizar o login. Tente novamente."
    ]);
}