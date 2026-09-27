<?php

session_start();

require_once "../model/conexao.php";

$logado = isset($_SESSION["id_usuario"]);

$nomeUsuario = "";
$nivelUsuario = "0 - Iniciante";
$statusCurso = "Você ainda não iniciou nenhum curso.";

if($logado){

    $nomeUsuario = $_SESSION["nome"];

}

/* Buscar os cursos */
$sqlCursos = "SELECT * FROM curso WHERE ativo = TRUE ORDER BY id_curso";
$stmtCursos = $pdo->prepare($sqlCursos);
$stmtCursos->execute();

$cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cursos - Sinalize Amor</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../CSS/cursosStyle.css">
</head>


<body>

<header class="headerSite">
    <div class="navbarPrincipal">

        <!-- LOGO -->
        <div class="logoSite">Sinalize<span>Amor</span></div>

        <ul class="menuPrincipal">
            <li>
                <a href="inicio.php"> Início </a>
            </li>

            <li>
                <a href="sobre.php"> Sobre </a>
            </li>

            <li>
                <a href="cursos.php"> Cursos </a>
            </li>

            <li>
                <a href="comunidade.php"> Comunidade </a>
            </li>

            <li>
                <a href="contato.php"> Contato </a>
            </li>
        </ul>


        <!-- BOTÃO VOLTAR -->
        <a href="inicio.php" class="botaoVoltar"> Voltar </a>

    </div>

    <div class="sloganSite"> A Caminho da Inclusão </div>
</header>


<section class="introducaoCursos">
    <div class="nivelUsuario">
        <?php if(!$logado): ?>

            📚 Você está no nível 0 - Iniciante

        <?php else: ?>

            👋 Olá,<?php echo htmlspecialchars($nomeUsuario); ?>!

            <br>

            📚 Seu nível: <?php echo $nivelUsuario; ?>

            <br>

            <small> <?php echo $statusCurso; ?> </small>

        <?php endif; ?>
    </div>

    <h1> Aprenda Libras do zero </h1>

    <p>
        Nossa trilha de aprendizado foi criada para ensinar Libras
        de forma simples, interativa e progressiva.

        Você aprenderá desde os primeiros sinais até construir
        conversas mais completas utilizando vídeos, exercícios e práticas.
    </p>
</section>


<section class="containerModulos">

    <h2 class="tituloModulos"> Trilha de Aprendizado </h2>

    <div class="listaModulos">
        <?php
        
        $sqlModulos = "
        SELECT 
        modulo.id_modulo,
        modulo.titulo,
        modulo.descricao,
        modulo.ordem,
        curso.titulo AS curso
    FROM modulo
    INNER JOIN curso 
        ON modulo.id_curso = curso.id_curso
    WHERE curso.ativo = TRUE
    ORDER BY modulo.id_curso, modulo.ordem
";

$stmtModulos = $pdo->prepare($sqlModulos);
$stmtModulos->execute();

$modulos = $stmtModulos->fetchAll(PDO::FETCH_ASSOC);

?>

<?php foreach($modulos as $modulo): ?>

    <div class="cardModulo">

        <h2>
            <?php echo htmlspecialchars($modulo["titulo"]); ?>
        </h2>

        <span class="nivelModulo">
            <?php echo htmlspecialchars($modulo["curso"]); ?>
        </span>

        <p class="resumoModulo">
            <?php echo htmlspecialchars($modulo["descricao"]); ?>
        </p>

        <div class="conteudoModulo">

            <p>
                Conteúdo do módulo será adicionado aqui.
            </p>

            <?php if($logado): ?>

                <a 
                    href="modulo.php?id=<?php echo $modulo["id_modulo"]; ?>" 
                    class="botaoComecarModulo"
                >
                    Começar Módulo
                </a>

            <?php else: ?>

                <a 
                    href="#" 
                    onclick="abrirLogin(); return false;" 
                    class="botaoComecarModulo"
                >
                    Começar Módulo
                </a>

            <?php endif; ?>

        </div>

    </div>

<?php endforeach; ?>
        

    </div>

</section>


<footer>
    © <?php echo date("Y"); ?> Sinalize Amor - Todos os direitos reservados.
</footer>

<div id="modalLogin" class="modalLogin">
    <div class="conteudoLogin">

        <!-- FECHAR -->
        <span onclick="fecharLogin()" class="fechar">

            ×

        </span>


        <h2> Entrar para continuar </h2>

        <p> Faça login para iniciar seu curso. </p>

        <form action="../control/login.php" method="POST">
            <input type="email" name="email" placeholder="Seu email" required>

            <input type="password" name="senha" placeholder="Sua senha" required>

            <button type="submit" class="botaoEntrarModal"> Entrar </button>
        </form>

        <p class="rodapeModal"> Ainda não possui conta?
            <a href="cadastro.php"> Criar conta </a>
        </p>
    </div>
</div>


<script>

function abrirLogin(){
    document.getElementById("modalLogin").style.display = "flex";
}


function fecharLogin(){
    document.getElementById("modalLogin").style.display = "none";
}


window.addEventListener("click", function(event){
    let modal = document.getElementById("modalLogin");

    if(event.target === modal){
        fecharLogin();
    }
});


document.addEventListener("keydown", function(event){

    if(event.key === "Escape"){
        fecharLogin();
    }
});

</script>
</body>
</html>