<?php

session_start();
require_once "../model/conexao.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../view/inicio.php");
    exit;
}

if (!empty($_SESSION["foto_perfil"])) {

    $caminho = "../uploads/perfils/" . $_SESSION["foto_perfil"];

    if (file_exists($caminho)) {
        unlink($caminho);
    }
}

$sql = $pdo->prepare("
    UPDATE usuario
    SET foto_perfil = NULL
    WHERE id_usuario = ?
");

$sql->execute([
    $_SESSION["id_usuario"]
]);

$_SESSION["foto_perfil"] = null;

header("Location: ../view/inicio.php");
exit;