<?php
session_start();
require_once "../model/conexao.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /tcc/view/inicio.php");
    exit;
}

$email = trim($_POST["email"]);
$senha = $_POST["senha"];

if (empty($email) || empty($senha)) {
    die("Preencha todos os campos.");
}

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
");


$sql->execute([$email]);

$usuario = $sql->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("E-mail ou senha inválidos.");
}

if (!password_verify($senha, $usuario["senha_hash"])) {
    die("E-mail ou senha inválidos.");
}

$_SESSION["id_usuario"] = $usuario["id_usuario"];
$_SESSION["nome"] = $usuario["nome"];
$_SESSION["sobrenome"] = $usuario["sobrenome"];
$_SESSION["username"] = $usuario["username"];
$_SESSION["email"] = $usuario["email"];
$_SESSION["foto_perfil"] = $usuario["foto_perfil"];

header("Location: ../view/inicio.php");
exit;