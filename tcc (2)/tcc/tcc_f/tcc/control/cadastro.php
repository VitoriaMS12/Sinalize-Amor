
<?php
session_start();
require_once __DIR__ . "/../model/conexao.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /tcc/view/inicio.php");
    exit;
}
$nome = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";
$confirmarSenha = $_POST["confirmarSenha"] ?? "";
if (
    $nome === "" ||
    $email === "" ||
    $senha === "" ||
    $confirmarSenha === ""
) {
    die("Preencha todos os campos.");
}

if (mb_strlen($nome, "UTF-8") > 201) {
    die("O nome informado é muito longo.");
}
if (
    strlen($email) > 150 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    die("Digite um e-mail válido.");
}
$email = mb_strtolower($email, "UTF-8");
if ($senha !== $confirmarSenha) {
    die("As senhas não coincidem.");
}
$partes = preg_split('/\s+/u', $nome, 2);
$primeiroNome = $partes[0];
$sobrenome = $partes[1] ?? "";
if (
    mb_strlen($primeiroNome, "UTF-8") > 100 ||
    mb_strlen($sobrenome, "UTF-8") > 100
) {
    die("O nome ou sobrenome é muito longo.");
}
$baseUsername = iconv(
    "UTF-8",
    "ASCII//TRANSLIT//IGNORE",
    $primeiroNome
);
if ($baseUsername === false) {
    $baseUsername = "";
}
$baseUsername = strtolower(
    preg_replace(
        '/[^a-zA-Z0-9]/',
        '',
        $baseUsername
    )
);
if ($baseUsername === "") {
    $baseUsername = "usuario";
}
$baseUsername = substr($baseUsername, 0, 40);
$senhaHash = password_hash(
    $senha,
    PASSWORD_DEFAULT
);
try {
    $sql = $pdo->prepare("
        SELECT id_usuario
        FROM usuario
        WHERE LOWER(email) = ?
        LIMIT 1
    ");
    $sql->execute([$email]);
    if ($sql->fetch()) {
        die("Este e-mail já está cadastrado.");
    }
    $username = $baseUsername;
    $contador = 1;
    while (true) {
        $verifica = $pdo->prepare("
            SELECT id_usuario
            FROM usuario
            WHERE username = ?
            LIMIT 1
        ");
        $verifica->execute([$username]);
        if (!$verifica->fetch()) {
            break;
        }
        $username = $baseUsername . $contador;
        $contador++;
    }
    $sql = $pdo->prepare("
        INSERT INTO usuario (
            nome,
            sobrenome,
            username,
            email,
            senha_hash
        )
        VALUES (?, ?, ?, ?, ?)
    ");
    $sql->execute([
        $primeiroNome,
        $sobrenome,
        $username,
        $email,
        $senhaHash
    ]);
    $idUsuario = (int) $pdo->lastInsertId();
    session_regenerate_id(true);
    $_SESSION["id_usuario"] = $idUsuario;
    $_SESSION["nome"] = $primeiroNome;
    $_SESSION["sobrenome"] = $sobrenome;
    $_SESSION["username"] = $username;
    $_SESSION["email"] = $email;
    $_SESSION["tipo_usuario"] = "usuario";
    $_SESSION["foto_perfil"] = "perfil.jpg";
    header("Location: /tcc/view/inicio.php");
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        die(
            "E-mail ou nome de usuário já cadastrado."
        );
    }
    error_log(
        "Erro no cadastro: " . $e->getMessage()
    );
    http_response_code(500);
    die(
        "Erro ao realizar cadastro. " .
        "Tente novamente."
    );
}
