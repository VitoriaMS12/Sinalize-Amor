
<?php
session_start();

require_once __DIR__ . "/../model/conexao.php";

// PERMITIR APENAS POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /tcc/view/inicio.php");
    exit;
}

// RECEBER DADOS
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";

// VALIDAR CAMPOS
if ($email === "" || $senha === "") {
    die("Preencha todos os campos.");
}

// VALIDAR FORMATO DO E-MAIL
if (
    strlen($email) > 150 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    die("Digite um e-mail válido.");
}

// PADRONIZAR E-MAIL
$email = mb_strtolower($email, "UTF-8");

try {

    // BUSCAR USUÁRIO
    $sql = $pdo->prepare("
        SELECT
            id_usuario,
            nome,
            sobrenome,
            username,
            email,
            senha_hash,
            tipo_usuario,
            foto_perfil,
            ativo
        FROM usuario
        WHERE LOWER(email) = ?
        LIMIT 1
    ");

    $sql->execute([$email]);

    $usuario = $sql->fetch(PDO::FETCH_ASSOC);

    // VERIFICAR CREDENCIAIS
    if (
        !$usuario ||
        !password_verify(
            $senha,
            $usuario["senha_hash"]
        )
    ) {
        die("E-mail ou senha inválidos.");
    }

    // VERIFICAR CONTA ATIVA
    if (!(bool) $usuario["ativo"]) {
        die(
            "Sua conta está desativada. " .
            "Entre em contato com o suporte."
        );
    }

    // RENOVAR IDENTIFICADOR DA SESSÃO
    session_regenerate_id(true);

    // SALVAR DADOS NA SESSÃO
    $_SESSION["id_usuario"] =
        (int) $usuario["id_usuario"];

    $_SESSION["nome"] =
        $usuario["nome"];

    $_SESSION["sobrenome"] =
        $usuario["sobrenome"];

    $_SESSION["username"] =
        $usuario["username"];

    $_SESSION["email"] =
        $usuario["email"];

    $_SESSION["tipo_usuario"] =
        $usuario["tipo_usuario"];

    $_SESSION["foto_perfil"] =
        !empty($usuario["foto_perfil"])
            ? $usuario["foto_perfil"]
            : "perfil.jpg";

    // REDIRECIONAMENTO
    header("Location: /tcc/view/inicio.php");
    exit;

} catch (PDOException $e) {

    error_log(
        "Erro no login: " . $e->getMessage()
    );

    http_response_code(500);

    die(
        "Ocorreu um erro ao realizar o login. " .
        "Tente novamente."
    );
}
