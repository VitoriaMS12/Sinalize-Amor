<?php
/* O arquivo vai receber a imagem pelo formulário,
 verificar se é uma imagem válida, salvar com um 
 nome único e atualizar o usuário.*/


session_start();
require_once "../model/conexao.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../view/inicio.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../view/inicio.php");
    exit;
}

if (!isset($_FILES["foto"]) || $_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
    die("Erro ao enviar a foto.");
}

$arquivo = $_FILES["foto"];

$tiposPermitidos = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/webp" => "webp"
];

$tipo = mime_content_type($arquivo["tmp_name"]);

if (!isset($tiposPermitidos[$tipo])) {
    die("A imagem deve ter no máximo 5 MB.");
}

$extensao = $tiposPermitidos[$tipo];

$nomeArquivo = "usuario_" . $_SESSION["id_usuario"] . "_" . uniqid() . "." . $extensao;

$pasta = "../uploads/perfils/";

if (!is_dir($pasta)) {
    mkdir($pasta, 0777, true);
}

$caminho = $pasta . $nomeArquivo;

if (!move_uploaded_file($arquivo["tmp_name"], $caminho)) {
    die("Não foi possível salvar a imagem.");
}

if (!empty($_SESSION["foto_perfil"])) {
    
    $fotoAntiga = $pasta . $_SESSION["foto_perfil"];

    if (file_exists($fotoAntiga)) {
        unlink($fotoAntiga);
    }

}

$sql = $pdo->prepare("
   UPDATE usuario
   SET foto_perfil = ?
   WHERE id_usuario = ?
");

$sql->execute([
    $nomeArquivo,
    $_SESSION["id_usuario"]
]);

$_SESSION["foto_perfil"] = $nomeArquivo;

header("Location: ../view/inicio.php");
exit;