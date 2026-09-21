<?php
session_start();

$logado = isset($_SESSION["id_usuario"]);
$nomeUsuario = "";

if ($logado) {
    $nomeUsuario = $_SESSION["nome"];

    /*if (empty($_SESSION["foto_perfil"])) {
        $_SESSION["foto_perfil"] = "../uploads/perfil.jpg";
    }*/
}
?>

<?php 

if (!empty($_SESSION["foto_perfil"])) {
    $fotoPerfil = "../uploads/perfils/" .$_SESSION["foto_perfil"];
} else {
    $fotoPerfil = "../uploads/perfil.jpg";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinalize Amor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/inicioStyle.css">
</head>

<body>
<header class="headerSite">
    <div class="navbarPrincipal">
        <div class="logoSite"> Sinalize<span>Amor</span></div>
        <ul class="menuPrincipal">
            <li><a href="sobre.php">Sobre</a></li>
            <li><a href="comunidade.php">Comunidade</a></li>
            <li><a href="contato.php">Contato</a></li>
        </ul>

        <?php if (!$logado): ?>
            <a href="#" class="botaoContato" onclick="abrirModalLogin(); return false;"> Entrar </a>
        <?php else: ?>
        
        <div class="usuarioHeader">
            <img src = "<?php echo htmlspecialchars($fotoPerfil); ?>" class="fotoPerfilTopo" id="fotoPerfilTopo" alt="Foto de Perfil">
            <a href="perfil.php" class="nomeUsuarioTopo" title="Seu perfil"> <?php echo htmlspecialchars($nomeUsuario); ?></a>
            <br>
            <a href="../control/logout.php" class="botaoSair"> <span class="iconeLogout">↪</span> Sair </a>
        </div>
        <?php endif; ?>
    </div>
<div class="sloganSite"> A Caminho da Inclusão </div>
</header>


<section class="heroInicio">
    <div class="containerHero">
        <div class="textoHero">
            <h1 class="tituloHero"> Aprenda Libras de forma simples, acolhedora e acessível.</h1>
            <p class="descricaoHero">
                Nosso objetivo é aproximar pessoas por meio da Língua Brasileira de Sinais, 
                oferecendo conteúdos teóricos e práticos para tornar a comunicação mais inclusiva.
            </p>

            <?php if ($logado): ?>
                <a href="cursos.php" class="botaoComecar"> Começar Agora </a>
            <?php else: ?>
                <a href="#" class="botaoComecar" onclick="abrirModalLogin(); return false;"> Começar Agora </a>
            <?php endif; ?>
        </div>
        
        <div class="imagemHero">
            <img src="../style/imagem1.jpg" alt="Ilustração Libras">
        </div>
    </div>
</section>


<section class="containerCards">
    <h2 class="tituloSessao"> Como você irá aprender? </h2>
    <p class="subtituloSessao"> Uma plataforma criada para ensinar Libras de maneira intuitiva e dinâmica.</p>
    
    <div class="cards">
        <div class="cardTeoria">
            <h2>📚 Conhecimento Teórico</h2>
            <p>
                Explore a parte teórica da Libras de forma simples e dinâmica. Aprenda os conceitos fundamentais, 
                a estrutura da língua e a importância da comunicação inclusiva, criando uma base sólida para colocar 
                o aprendizado em prática.
            </p>
        </div>
        
        <div class="cardPratica">
            <h2>🎥 Conhecimento Prático</h2>
            <p>Desenvolva sua comunicação em Libras assistindo a vídeos demonstrativos que ensinam os sinais de 
                forma clara e objetiva, facilitando o aprendizado e a memorização.
            </p>
        </div>
        
        <div class="cardInclusao">
            <h2>🤝 Exercicíos Constantes</h2>
            <p>Ao final de cada aula, você terá acesso a exercícios desenvolvidos para reforçar o conteúdo aprendido. 
                Coloque seus conhecimentos em prática, teste sua compreensão e acompanhe sua evolução por meio de 
                atividades que facilitam a fixação dos sinais e conceitos da Libras.
            </p>
        </div>
    </div>
</section>


<section class="secaoSobre">
    <div class="containerSobre">
        <div class="textoSobre">
            <h2>Por que aprender Libras?</h2>
            <p>
                A Língua Brasileira de Sinais é muito mais do que uma forma de comunicação. Ela representa identidade, 
                cultura e inclusão. Ao aprender Libras, você contribui para uma sociedade mais acessível, amplia suas 
                oportunidades pessoais e profissionais e fortalece a conexão com a comunidade surda.
            </p>
        </div>

        <div class="estatisticas">
            <div class="cardEstatistica">
                <h3>📖</h3>
                <span>Conteúdo Teórico</span>
            </div>

            <div class="cardEstatistica">
                <h3>🎥</h3>
                <span>Vídeos Explicativos</span>
            </div>

            <div class="cardEstatistica">
                <h3>🤝</h3>
                <span>Exercicíos Práticos</span>
            </div>

            <div class="cardEstatistica">
                <h3>💙</h3>
                <span>Aprenda no seu ritmo</span>
            </div>
        </div>
    </div>
</section>


<footer class="rodapeSite">
    <h2>Sinalize<span>Amor</span></h2>
    <p>
        Tornando a comunicação mais acessível através da Língua Brasileira de Sinais.
    </p>

    <p class="copyright">
        © <?php echo date("Y"); ?> Sinalize Amor • Todos os direitos reservados.
    </p>
</footer>


<div id="modalLogin" class="modalLogin">
    <div class="containerModalLogin">
        <span class="botaoFecharModal" onclick="fecharModalLogin()">&times;</span>
        <h2>Bem-vindo!</h2>
        <p>
            Entre para continuar sua jornada no Sinalize Amor.
        </p>
        <form action="../control/login.php" method="POST">
            <input type="email" name="email" placeholder="E-mail" required>

            <input type="password" name="senha" placeholder="Senha" required>

            <button type="submit" class="botaoEntrarModal"> Entrar </button>
        </form>

        <div class="rodapeModal">
            <p> Ainda não possui uma conta?
            <a href="#" onclick="abrirModalCadastro(); return false;"> Criar conta</a>
            </p>
        </div>
    </div>
</div>


<!-- FORMULÁRIO DE CADASTRO!!!!! -->
<div id="modalCadastro" class="modalLogin">
    <div class="containerModalLogin">
        <span class="botaoFecharModal" onclick="fecharModalCadastro()">&times;</span>
        <h2>Criar conta</h2>
        <p>Preencha os dados para começar.</p>
        
        <form action="../control/cadastro.php" method="POST">
            <input type="hidden" name="origem" value="inicio">

            <input type="text" name="nome" placeholder="Nome" required>

            <input type="email" name="email" placeholder="E-mail" required>

            <input type="password" name="senha" placeholder="Senha" required>

            <input type="password" name="confirmarSenha" placeholder="Confirmar senha" required>

            <button type="submit" class="botaoEntrarModal"> Criar conta</button>
        </form>

        <div class="rodapeModal">
            <p> Já possui uma conta?
                <a href="#" onclick="abrirModalLogin(); fecharModalCadastro(); return false;"> Entrar</a>
            </p>
        </div>
    </div>
</div>

<div id="modalFotoPerfil" class="modalLogin">
    <div style="position:relative; text-align:center;">
        
        <span class="botaoFecharModal" onclick="fecharFotoPerfil()">&times;</span>

        <img id="imagemPerfilGrande" src="<?php echo htmlspecialchars($fotoPerfil); ?>" style=" width:420px;
        max-width:90vw; max-height:70vh; object-radius:20px; border-radius:20px; box-shadow:0 20px 50px rgba(0,0,0,.35);">

        <br>

        <button type="button" onclick="abrirOpcoesFoto()" style=" margin-top:15px; border:none; background:#7EC8E3;
        color:white; border-radius:50%; width:50px; height:50px; font-size:22px; cursor:pointer;">
        </button>

        <div id="opcoesFoto" style=" display:none; margin-top:15px;">
            <form action="../control/trocar_foto.php" method="POST" enctype="multipart/form-data">

                <label for="novaFoto" style=" display:block; padding:12px 20px; background:#7EC8E3; color:white;
                border-radius:10px;cursor:pointer; margin-bottom:10px;"> Trocar foto </label>

                <input type="file" id="novaFoto" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none;"
                onchange="this.form.submit();">
            </form>

            <?php if (!empty($_SESSION["foto_perfil"])): ?>

                <form action="../control/excluir_foto.php" method="POST">
                    <button type="submit" style=" width:100%; padding:12px 20px; background:#d9534f; color:white; 
                    border:none; border-radius:10px; cursor:pointer;"> Excluir foto
                    </button>
                </form>

            <?php endif; ?>

        </div>
    </div>
</div>


<script>
function abrirModalLogin(){
    document.getElementById("modalLogin").style.display = "flex";
}


function fecharModalLogin(){
    document.getElementById("modalLogin").style.display = "none";
}


window.addEventListener("click", function(event){
    let modal = document.getElementById("modalLogin");
    if(event.target === modal){
        modal.style.display = "none";
    }
});


document.addEventListener("keydown", function(event){
    if(event.key === "Escape"){
        fecharModalLogin();
    }
});


function abrirModalCadastro(){
    fecharModalLogin();
    document.getElementById("modalCadastro").style.display = "flex";
}


function fecharModalCadastro(){
    document.getElementById("modalCadastro").style.display = "none";
}


document.addEventListener("keydown", function(event){
    if(event.key === "Escape"){
        fecharModalLogin();
        fecharModalCadastro();
    }
});


window.addEventListener("click", function(event){
    if(event.target === document.getElementById("modalLogin")){
        fecharModalLogin();
    }
    if(event.target === document.getElementById("modalCadastro")){
        fecharModalCadastro();
    }
});


const foto = document.getElementById("fotoPerfilTopo");

if(foto){
    foto.addEventListener("click",function(){
        document.getElementById("imagemPerfilGrande").src=this.src;
        document.getElementById("modalFotoPerfil").style.display="flex";
    });
}

function abrirOpcoesFoto(){

    const opcoes = document.getElementById("opcoesFoto");

    if(opcoes.style.display === "none"){
        opcoes.style.display = "block";
    } else {
        opcoes.style.display = "none";
    }
}


function fecharFotoPerfil(){
    document.getElementById("modalFotoPerfil").style.display="none";
}


window.addEventListener("click",function(e){
    if(e.target==document.getElementById("modalFotoPerfil")){
        fecharFotoPerfil();
    }
});


</script>
</body>
</html>