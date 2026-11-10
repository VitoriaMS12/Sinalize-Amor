<?php

// Conecta com o banco de dados.
require_once "../model/conexao.php";

// Define que a resposta será JSON.
header("Content-Type: application/json; charset=utf-8");

// Aceita apenas requisições POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido."
    ]);

    exit;
}

// Recebe os dados enviados pelo aplicativo.
$nome = trim($_POST["nome"] ?? "");
$sobrenome = trim($_POST["sobrenome"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";

// Verifica se todos os campos obrigatórios foram preenchidos.
if (
    $nome === "" ||
    $sobrenome === "" ||
    $username === "" ||
    $email === "" ||
    trim($senha) === ""
) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Preencha todos os campos."
    ]);

    exit;
}

// Verifica o formato do e-mail.
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Digite um e-mail válido."
    ]);

    exit;
}

// Verifica se o e-mail ou o nome de usuário já existem.
$sql = $pdo->prepare("
    SELECT email, username
    FROM usuario
    WHERE email = ? OR username = ?
");

$sql->execute([$email, $username]);
$existente = $sql->fetch(PDO::FETCH_ASSOC);

if ($existente) {
    if ($existente["email"] === $email) {
        $mensagem = "Este e-mail já está cadastrado.";
    } else {
        $mensagem = "Este nome de usuário já está em uso.";
    }

    echo json_encode([
        "sucesso" => false,
        "mensagem" => $mensagem
    ]);

    exit;
}

// Protege a senha antes de armazená-la no banco.
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere a nova conta.
// Os demais campos da tabela utilizam seus valores padrão
// ou aceitam NULL, conforme a estrutura do banco.
$sql = $pdo->prepare("
    INSERT INTO usuario
        (nome, sobrenome, username, email, senha_hash)
    VALUES (?, ?, ?, ?, ?)
");

try {
    $sql->execute([
        $nome,
        $sobrenome,
        $username,
        $email,
        $senhaHash
    ]);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Conta criada com sucesso."
    ]);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível criar a conta. Tente novamente."
    ]);
}
