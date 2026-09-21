<?php

session_start();

$logado = isset($_SESSION["id_usuario"]);

$nomeUsuario = "";
$nivelUsuario = "0 - Iniciante";
$statusCurso = "Você ainda não iniciou nenhum curso.";

if($logado){

    $nomeUsuario = $_SESSION["nome"];

}

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

        <div class="cardModulo">
            <h2> 🤟 Básico 1 </h2>
            <span class="nivelModulo"> Primeiros passos </span>
            <p class="resumoModulo"> Aprenda os primeiros conceitos e sinais da Libras. </p>

            <div class="conteudoModulo">

                <p> Conteúdo do módulo:
                    <br>

                    ° Alfabeto manual
                    <br>

                    ° Números manuais
                    <br>

                    ° Saudações
                    <br>

                    ° Apresentações
                    <br>

                    ° Pronomes
                    <br>

                    ° Pessoas
                    <br>

                    ° Cores
                    <br>

                    ° Tempo
                    <br>

                    ° Calendário
                    <br>

                    ° Perguntas
                </p>

                <?php if($logado): ?>
                    <a href="moduloa.php" class="botaoComecarModulo"> Começar Módulo </a>
                <?php else: ?>
                    <a href="#" onclick="abrirLogin(); return false;" class="botaoComecarModulo"> Começar Módulo </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="cardModulo">
            <h2> 🤟 Básico 2 </h2>
            <span class="nivelModulo"> Construindo vocabulário </span>
            <p class="resumoModulo"> Amplie seus conhecimentos com novos sinais. </p>

            <div class="conteudoModulo">

                <p> Conteúdo do módulo:
                    <br>

                    ° Estações do ano
                    <br>

                    ° Ações (verbos)
                    <br>

                    ° Negação
                    <br>

                    ° Adjetivos
                    <br>

                    ° Emoções
                    <br>

                    ° Alimentos
                    <br>

                    ° Lugares
                    <br>

                    ° Objetos 
                </p>


                <?php if($logado): ?>
                    <a href="modulob.php" class="botaoComecarModulo"> Começar Módulo </a>
                <?php else: ?>
                    <a href="#" onclick="abrirLogin(); return false;" class="botaoComecarModulo"> Começar Módulo </a>
                <?php endif; ?>
            </div>
        </div>


        <div class="cardModulo">
            <h2> 🤟 Básico 3 </h2>
            <span class="nivelModulo"> Comunicação inicial </span>
            <p class="resumoModulo"> Comece a formar frases e pequenos diálogos. </p>

            <div class="conteudoModulo">
                <p>
                    Neste módulo você aprenderá estrutura das
                    frases, expressões faciais e pequenas conversas
                    em Libras.
                </p>

                <?php if($logado): ?>
                    <a href="moduloc.php" class="botaoComecarModulo"> Começar Módulo </a>
                <?php else: ?>
                    <a href="#" onclick="abrirLogin(); return false;" class="botaoComecarModulo"> Começar Módulo </a>
                <?php endif; ?>
            </div>
        </div>


        <div class="cardModulo">
            <h2>💙 Intermediário 1</h2>
            <span class="nivelModulo"> Aprimorando comunicação </span>
            <p class="resumoModulo"> Desenvolva mais confiança ao sinalizar. </p>

            <div class="conteudoModulo">

                <p>
                    Você aprenderá novos sinais, classificadores,
                    expressões e situações reais de comunicação.
                </p>


                <?php if($logado): ?>
                    <a href="modulod.php" class="botaoComecarModulo"> Começar Módulo </a>
                <?php else: ?>
                    <a href="#" onclick="abrirLogin(); return false;" class="botaoComecarModulo"> Começar Módulo </a>
                <?php endif; ?>
            </div>
        </div>


        <div class="cardModulo">
            <h2> 💙 Intermediário 2 </h2>
            <span class="nivelModulo"> Fluência em desenvolvimento </span>
            <p class="resumoModulo"> Melhore sua compreensão e interpretação. </p>


            <div class="conteudoModulo">
                <p>
                    Neste módulo serão trabalhados diálogos maiores,
                    interpretação visual e variações da Libras.
                </p>

                <?php if($logado): ?>
                    <a href="moduloe.php" class="botaoComecarModulo"> Começar Módulo </a>
                <?php else: ?>
                    <a href="#" onclick="abrirLogin(); return false;" class="botaoComecarModulo"> Começar Módulo </a>
                <?php endif; ?>
            </div>
        </div>


        <div class="cardModulo">
            <h2>💙 Intermediário 3 </h2>
            <span class="nivelModulo"> Comunicação avançada</span>
            <p class="resumoModulo"> Prepare-se para utilizar Libras em diferentes contextos. </p>

            <div class="conteudoModulo">
                <p>
                    Você desenvolverá conversação, interpretação,
                    vocabulário profissional e comunicação natural.
                </p>

                <?php if($logado): ?>
                    <a href="modulof.php" class="botaoComecarModulo"> Começar Módulo</a>
                <?php else: ?>
                    <a href="#" onclick="abrirLogin(); return false;" class="botaoComecarModulo"> Começar Módulo </a>
                <?php endif; ?>
            </div>
        </div>

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