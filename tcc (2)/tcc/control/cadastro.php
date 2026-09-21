<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../model/conexao.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../view/inicio.php");
    exit;
}


$nome = trim($_POST["nome"]);
$email = trim($_POST["email"]);
$senha = $_POST["senha"];
$confirmarSenha = $_POST["confirmarSenha"];

if (empty($nome) || empty($email) || empty($senha) || empty($confirmarSenha)) {
    die("Preencha todos os campos.");
}

if ($senha !== $confirmarSenha) {
    die("As senhas não coincidem.");
}

$sql = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$sql->execute([$email]);

if ($sql->fetch()) {
    die("Este e-mail já está cadastrado.");
}

$partes = explode(" ", $nome, 2);

$primeiroNome = $partes[0];
$sobrenome = isset($partes[1]) ? $partes[1] : "";

$username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $primeiroNome));

$contador = 1;

while (true) {

    $verifica = $pdo->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
    $verifica->execute([$username]);

    if (!$verifica->fetch()) {
        break;
    }

    $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $primeiroNome)) . $contador;
    $contador++;
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$sql = $pdo->prepare("
    INSERT INTO usuario
    (nome, sobrenome, username, email, senha_hash)

    VALUES
    (?, ?,  ?, ?, ?)
");

$sql->execute([
    $primeiroNome,
    $sobrenome,
    $username,
    $email,
    $senhaHash
]);
$idUsuario = $pdo->lastInsertId();

$sql = $pdo->prepare("
    SELECT id_usuario, nome, username
    FROM usuario
    WHERE id_usuario = ?
");

$sql->execute([$idUsuario]);

$usuario = $sql->fetch(PDO::FETCH_ASSOC);

$_SESSION["id_usuario"] = $usuario["id_usuario"];
$_SESSION["nome"] = $usuario["nome"];
$_SESSION["username"] = $usuario["username"];

header("Location: ../view/inicio.php");
exit;