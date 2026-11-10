<?php

// Conecta com o banco de dados.
require_once "../model/conexao.php";

header("Content-Type: application/json; charset=utf-8");

// Verifica se a requisição é GET.
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido."
    ]);
    exit;
}

// Recebe o ID do curso enviado pelo aplicativo.
$id_curso = $_GET["id_curso"] ?? "";

if (empty($id_curso)) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "ID do curso não informado.",
        "modulos" => []
    ]);
    exit;
}

// Busca os módulos pertencentes ao curso.
$sql = $pdo->prepare("
    SELECT
        id_modulo,
        id_curso,
        titulo,
        descricao,
        ordem
    FROM modulo
    WHERE id_curso = ?
    ORDER BY ordem
");

$sql->execute([$id_curso]);

$modulos = $sql->fetchAll(PDO::FETCH_ASSOC);

// Retorna os módulos em JSON.
echo json_encode([
    "sucesso" => true,
    "mensagem" => "Módulos encontrados.",
    "modulos" => $modulos
]);