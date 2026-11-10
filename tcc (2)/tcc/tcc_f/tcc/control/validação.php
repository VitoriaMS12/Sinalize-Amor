
<?php

function validarEmail(string $email): bool {
    $email = trim($email);

    return filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    ) !== false && strlen($email) <= 150;
}

function validarSenha(string $senha): bool {
    return $senha !== '';
}

function confirmarSenha(
    string $senha,
    string $confirmacao
): bool {
    return $senha === $confirmacao;
}
