<?php

// Conecta com o banco de dados.
require_once "../model/conexao.php";

//Diz que a resposta sera em JSON.
header("Content-Type: application/json; charset=utf-8");

// Verifica se o app enviou a requisição usando GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido."
    ]);

    exit;
}

// Busca somente os cursos que estão ativos.
$sql = $pdo->prepare("
    SELECT
        id_curso,
        titulo,
        descricao,
        imagem,
        ativo,
        data_criacao
    FROM curso
    WHERE ativo = TRUE
    ORDER BY id_curso
");

// Executa a consulta.
$sql->execute();

// Pega todos os cursos encontrados.
$cursos = $sql->fetchAll(PDO::FETCH_ASSOC);

// Envia os cursos para app.
echo json_encode([
    "sucesso" => true,
    "mensagem" => "Cursos encontrados.",
    "cursos" => $cursos
]);