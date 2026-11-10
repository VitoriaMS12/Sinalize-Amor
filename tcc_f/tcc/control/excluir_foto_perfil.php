
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

try {
    // Busca o nome da foto atual.
    $consulta = $pdo->prepare(
        "SELECT foto_perfil FROM usuario
         WHERE id_usuario = ? LIMIT 1"
    );
    $consulta->execute([$_SESSION["id_usuario"]]);
    $fotoAtual = $consulta->fetchColumn();

    // Remove a referência da foto no banco.
    $atualizar = $pdo->prepare(
        "UPDATE usuario SET foto_perfil = NULL
         WHERE id_usuario = ?"
    );
    $atualizar->execute([$_SESSION["id_usuario"]]);

    // A sessão passa a usar o ícone padrão.
    $_SESSION["foto_perfil"] = null;

    // Apaga o arquivo antigo do servidor.
    if ($fotoAtual && basename($fotoAtual) === $fotoAtual) {
        $caminho = dirname(__DIR__)
            . DIRECTORY_SEPARATOR . "uploads"
            . DIRECTORY_SEPARATOR . "perfis"
            . DIRECTORY_SEPARATOR . $fotoAtual;

        if (is_file($caminho)) {
            @unlink($caminho);
        }
    }
} catch (Throwable $e) {
    header("Location: ../view/inicio.php?erro_foto=excluir");
    exit;
}

header("Location: ../view/inicio.php?foto=excluida");
exit;
?>
