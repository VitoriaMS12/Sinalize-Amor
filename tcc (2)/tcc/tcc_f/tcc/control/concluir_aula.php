
<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../View/inicio.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Método não permitido.");
}

require_once "../model/conexao.php";

// Validar token CSRF
$token = $_POST["csrf_token"] ?? "";

if (
    empty($_SESSION["csrf_token"]) ||
    !is_string($token) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    http_response_code(403);
    exit("Solicitação inválida.");
}

$idUsuario = (int) $_SESSION["id_usuario"];

$idModulo = filter_input(
    INPUT_POST,
    "id_modulo",
    FILTER_VALIDATE_INT
);

$idAula = filter_input(
    INPUT_POST,
    "id_aula",
    FILTER_VALIDATE_INT
);

if (!$idModulo || !$idAula ||
    $idModulo < 1 || $idAula < 1) {
    http_response_code(400);
    exit("Dados inválidos.");
}

// Verificar se a aula existe e pertence ao módulo
$sql = "SELECT a.id_aula
        FROM aulas a
        INNER JOIN modulos m
            ON m.id_modulo = a.id_modulo
        WHERE a.id_aula = ?
        AND a.id_modulo = ?
        AND a.ativo = 1
        AND m.ativo = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idAula, $idModulo]);

if (!$stmt->fetch()) {
    http_response_code(404);
    exit("Aula não encontrada.");
}

// Registrar conclusão
$sql = "INSERT INTO progresso_aulas
            (id_usuario, id_aula, concluida, data_conclusao)
        VALUES (?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE
            concluida = 1,
            data_conclusao = COALESCE(
                data_conclusao,
                NOW()
            )";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idUsuario, $idAula]);

header(
    "Location: ../View/modulo.php?id=" .
    $idModulo .
    "&aula=" .
    $idAula .
    "&sucesso=1"
);

exit;
?>
