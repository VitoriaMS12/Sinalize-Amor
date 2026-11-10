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

// Recebe o ID do módulo enviado pelo aplicativo.
$id_modulo = $_GET["id_modulo"] ?? "";

if ($id_modulo === "" || !ctype_digit((string)$id_modulo) || (int)$id_modulo <= 0) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "ID do módulo inválido.",
        "aulas" => []
    ]);
    exit;
}

try {
    // Busca as aulas ativas do módulo.
    $sql = $pdo->prepare("
        SELECT
            id_aula,
            id_modulo,
            titulo,
            descricao,
            conteudo,
            video,
            imagem,
            duracao_minutos,
            ordem,
            data_criacao,
            data_atualizacao
        FROM aula
        WHERE id_modulo = ?
          AND ativo = 1
        ORDER BY ordem
    ");

    $sql->execute([(int)$id_modulo]);

    $aulas = $sql->fetchAll(PDO::FETCH_ASSOC);

    // Busca os materiais de cada aula.
    $sqlMateriais = $pdo->prepare("
        SELECT
            id_material,
            titulo,
            arquivo,
            tipo,
            ordem
        FROM material_aula
        WHERE id_aula = ?
          AND ativo = 1
        ORDER BY ordem
    ");

    foreach ($aulas as &$aula) {
        $sqlMateriais->execute([$aula["id_aula"]]);
        $aula["materiais"] = $sqlMateriais->fetchAll(PDO::FETCH_ASSOC);
    }

    unset($aula);

    // Retorna as aulas e seus materiais em JSON.
    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Aulas encontradas.",
        "aulas" => $aulas
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao buscar as aulas.",
        "aulas" => []
    ], JSON_UNESCAPED_UNICODE);
}