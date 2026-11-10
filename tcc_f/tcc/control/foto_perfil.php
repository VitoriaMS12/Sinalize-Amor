
<?php
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

if (
    !isset($_FILES["foto_perfil"]) ||
    $_FILES["foto_perfil"]["error"] !== UPLOAD_ERR_OK
) {
    header("Location: ../view/inicio.php?erro_foto=upload");
    exit;
}

$arquivo = $_FILES["foto_perfil"];
$limiteBytes = 5 * 1024 * 1024; // Limite de 5 MB

if ($arquivo["size"] <= 0 || $arquivo["size"] > $limiteBytes) {
    header("Location: ../view/inicio.php?erro_foto=tamanho");
    exit;
}

// Verifica o tipo real da imagem.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($arquivo["tmp_name"]);

$tiposPermitidos = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp"
];

if (
    !isset($tiposPermitidos[$mime]) ||
    @getimagesize($arquivo["tmp_name"]) === false
) {
    header("Location: ../view/inicio.php?erro_foto=formato");
    exit;
}

// Pasta onde as fotos serão armazenadas.
$pasta = dirname(__DIR__) . DIRECTORY_SEPARATOR
       . "uploads" . DIRECTORY_SEPARATOR . "perfis";

if (
    !is_dir($pasta) &&
    !mkdir($pasta, 0755, true) &&
    !is_dir($pasta)
) {
    header("Location: ../view/inicio.php?erro_foto=pasta");
    exit;
}

// Gera um nome único para a imagem.
$extensao = $tiposPermitidos[$mime];
$nomeArquivo = "usuario_" . (int) $_SESSION["id_usuario"]
    . "_" . bin2hex(random_bytes(16)) . "." . $extensao;

$destino = $pasta . DIRECTORY_SEPARATOR . $nomeArquivo;

if (!move_uploaded_file($arquivo["tmp_name"], $destino)) {
    header("Location: ../view/inicio.php?erro_foto=salvar");
    exit;
}

try {
    // Busca a foto anterior.
    $consulta = $pdo->prepare(
        "SELECT foto_perfil FROM usuario
         WHERE id_usuario = ? LIMIT 1"
    );
    $consulta->execute([$_SESSION["id_usuario"]]);
    $fotoAntiga = $consulta->fetchColumn();

    // Atualiza a foto no banco de dados.
    $atualizar = $pdo->prepare(
        "UPDATE usuario SET foto_perfil = ?
         WHERE id_usuario = ?"
    );
    $atualizar->execute([
        $nomeArquivo,
        $_SESSION["id_usuario"]
    ]);

    // Atualiza a sessão.
    $_SESSION["foto_perfil"] = $nomeArquivo;

    // Apaga a imagem antiga após salvar a nova.
    if ($fotoAntiga && basename($fotoAntiga) === $fotoAntiga) {
        $caminhoAntigo = $pasta . DIRECTORY_SEPARATOR . $fotoAntiga;

        if (is_file($caminhoAntigo) && $fotoAntiga !== $nomeArquivo) {
            @unlink($caminhoAntigo);
        }
    }
} catch (Throwable $e) {
    if (is_file($destino)) {
        @unlink($destino);
    }

    header("Location: ../view/inicio.php?erro_foto=banco");
    exit;
}

header("Location: ../view/inicio.php?foto=atualizada");
exit;
?>
