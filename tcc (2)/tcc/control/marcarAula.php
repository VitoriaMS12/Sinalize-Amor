<?php

session_start();

require_once "../model/conexao.php";


if (!isset($_SESSION["id_usuario"])) {
    die("Você precisa estar logado.");
}


if (!isset($_POST["id_aula"])) {
    die("Aula não informada.");
}


$idUsuario = $_SESSION["id_usuario"];
$idAula = (int) $_POST["id_aula"];


$sql = "
    INSERT INTO progresso
    (id_usuario, id_aula, concluida, data_conclusao)

    VALUES
    (:id_usuario, :id_aula, TRUE, NOW())

    ON DUPLICATE KEY UPDATE
        concluida = TRUE,
        data_conclusao = NOW()
";


$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":id_usuario" => $idUsuario,
    ":id_aula" => $idAula
]);


/* Volta para a aula */
header("Location: ../view/modulo.php?id=" . $_POST["id_modulo"] . "&aula=" . $idAula);

exit;