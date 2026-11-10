
<?php
require_once "../model/conexao.php";

$email = "admgeral@controle.com";
$senha = "sinalizeamoradm";
$hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $sql = $pdo->prepare(
        "SELECT id_usuario FROM usuario WHERE email = ?"
    );
    $sql->execute([$email]);

    $admin = $sql->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Atualiza a conta existente
        $sql = $pdo->prepare("
            UPDATE usuario
            SET senha_hash = ?,
                tipo_usuario = 'administrador',
                ativo = 1
            WHERE id_usuario = ?
        ");

        $sql->execute([$hash, $admin["id_usuario"]]);

    } else {
        // Cria a conta de administrador
        $sql = $pdo->prepare("
            INSERT INTO usuario
                (nome, sobrenome, username, email,
                 senha_hash, tipo_usuario, ativo)
            VALUES
                (?, ?, ?, ?, ?, 'administrador', 1)
        ");

        $sql->execute([
            "Administrador",
            "Geral",
            "admgeral_controle",
            $email,
            $hash
        ]);
    }

    echo "Conta de administrador configurada com sucesso!";

} catch (PDOException $e) {
    echo "Erro ao configurar administrador. Verifique os dados do banco.";
}
