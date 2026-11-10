
<?php
session_start();

$logado = isset($_SESSION["id_usuario"]);
$nomeUsuario = "";
$ehAdmin = false;
$fotoPerfilArquivo = null;
$fotoPerfilUrl = "../style/usuario-padrao.svg";

if ($logado) {
    $nomeUsuario = $_SESSION["nome"] ?? "Usuário";
    $ehAdmin = (($_SESSION["tipo_usuario"] ?? "") === "administrador");

    $fotoPerfilArquivo = $_SESSION["foto_perfil"] ?? null;

    if (!empty($fotoPerfilArquivo)) {
        $fotoPerfilArquivo = basename($fotoPerfilArquivo);

        $caminhoFoto = __DIR__ . "/../uploads/perfis/" . $fotoPerfilArquivo;

        if (is_file($caminhoFoto)) {
            $fotoPerfilUrl = "../uploads/perfis/" . rawurlencode($fotoPerfilArquivo);
        } else {
            $_SESSION["foto_perfil"] = null;
            $fotoPerfilArquivo = null;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinalize Amor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}
html{
    scroll-behavior:smooth;
}
body{
    background:#FDFBF8;
    color:#555;
}
.headerSite{
    background:#EAF7FC;
    box-shadow:0 3px 15px rgba(0,0,0,.08);
    position:sticky;
    top:0;
    z-index:1000;
}
.navbarPrincipal{
    width:90%;
    max-width:1300px;
    margin:auto;
    height:85px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.logoSite{
    font-size:34px;
    font-weight:700;
    color:#6F5B47;
}
.logoSite span{
    color:#7EC8E3;
}
.menuPrincipal{
    list-style:none;
    display:flex;
    gap:35px;
}
.menuPrincipal li{
    list-style:none;
}
.menuPrincipal a{
    text-decoration:none;
    color:#6F5B47;
    font-weight:500;
    transition:.3s;
}
.menuPrincipal a:hover{
    color:#7EC8E3;
}
.botaoContato{
    background:#7EC8E3;
    color:white;
    padding:12px 24px;
    border-radius:30px;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}
.botaoContato:hover{
    background:#68B8D8;
}
.sloganSite{
    text-align:center;
    padding-bottom:15px;
    color:#A68A64;
    font-style:italic;
}
.heroInicio{
    min-height:82vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#EAF7FC,#F7F1E8);
}
.containerHero{
    width:90%;
    max-width:1300px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:60px;
}
.textoHero{
    flex:1;
}
.tituloHero{
    font-size:58px;
    color:#6F5B47;
    line-height:1.2;
    margin-bottom:25px;
}
.descricaoHero{
    font-size:20px;
    color:#666;
    line-height:1.8;
}
.botaoComecar{
    display:inline-block;
    margin-top:35px;
    background:#7EC8E3;
    color:white;
    text-decoration:none;
    padding:16px 38px;
    border-radius:35px;
    font-weight:600;
    transition:.3s;
    box-shadow:0 8px 20px rgba(126,200,227,.35);
}
.botaoComecar:hover{
    transform:translateY(-5px);
    background:#68B8D8;
}
.imagemHero{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
}

.imagemHero img{
    width:100%;
    max-width:500px;
    mix-blend-mode:multiply;
    display:block;
}
.containerCards{
    width:90%;
    max-width:1250px;
    margin:80px auto;
}
.tituloSessao{
    text-align:center;
    color:#6F5B47;
    font-size:38px;
    margin-bottom:15px;
}
.subtituloSessao{
    text-align:center;
    color:#777;
    margin-bottom:50px;
}
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:35px;
}
.cardTeoria,
.cardPratica,
.cardInclusao{
    background:white;
    padding:35px;
    border-radius:18px;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
    transition:.4s;
}
.cardTeoria{
    border-top:6px solid #7EC8E3;
}
.cardPratica{
    border-top:6px solid #A68A64;
}
.cardInclusao{
    border-top:6px solid #CDB79E;
}
.cardTeoria:hover,
.cardPratica:hover,
.cardInclusao:hover{
    transform:translateY(-10px);
}
.cardTeoria h2,
.cardPratica h2,
.cardInclusao h2{
    color:#6F5B47;
    margin-bottom:20px;
}
.cardTeoria p,
.cardPratica p,
.cardInclusao p{
    line-height:1.8;
    color:#666;
}
.secaoSobre{
    background:#EAF7FC;
    padding:80px 0;
}
.containerSobre{
    width:90%;
    max-width:1200px;
    margin:auto;
}
.textoSobre{
    text-align:center;
    margin-bottom:60px;
}
.textoSobre h2{
    color:#6F5B47;
    font-size:38px;
    margin-bottom:20px;
}
.textoSobre p{
    color:#666;
    line-height:1.9;
    max-width:900px;
    margin:auto;
}
.estatisticas{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:25px;
}
.cardEstatistica{
    background:white;
    border-radius:18px;
    padding:35px;
    text-align:center;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
    transition:.3s;
}
.cardEstatistica:hover{
    transform:translateY(-8px);
}
.cardEstatistica h3{
    font-size:45px;
    margin-bottom:18px;
}
.cardEstatistica span{
    color:#6F5B47;
    font-weight:600;
}
.rodapeSite{
    background:#F7F1E8;
    text-align:center;
    padding:50px 20px;
}
.rodapeSite h2{
    color:#6F5B47;
    font-size:32px;
    margin-bottom:15px;
}
.rodapeSite span{
    color:#7EC8E3;
}
.rodapeSite p{
    color:#777;
    line-height:1.8;
}
.copyright{
    margin-top:25px;
    font-size:14px;
}
@media(max-width:900px){
.containerHero{
    flex-direction:column-reverse;
    text-align:center;
}
.tituloHero{
    font-size:42px;
}
.descricaoHero{
    font-size:18px;
}
.navbarPrincipal{
    flex-direction:column;
    height:auto;
    padding:25px 0;
    gap:20px;
}
.menuPrincipal{
    flex-wrap:wrap;
    justify-content:center;
}
}
@media(max-width:600px){
.tituloHero{
    font-size:34px;
}
.tituloSessao{
    font-size:30px;
}
.textoSobre h2{
    font-size:30px;
}
.logoSite{
    font-size:28px;
}
}
.modalLogin{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,.45);
    display:none;
    justify-content:center;
    align-items:center;
    backdrop-filter:blur(4px);
    z-index:9999;
}
.containerModalLogin{
    width:420px;
    max-width:92%;
    background:#FFFDFB;
    border-radius:20px;
    padding:40px;
    position:relative;
    animation:abrirModal .35s ease;
    box-shadow:0 20px 50px rgba(0,0,0,.18);
}
.containerModalLogin h2{
    color:#6F5B47;
    text-align:center;
    margin-bottom:10px;
}
.containerModalLogin p{
    text-align:center;
    color:#777;
    margin-bottom:25px;
}
.containerModalLogin input{
    width:100%;
    padding:14px;
    margin-bottom:18px;
    border:1px solid #D7D7D7;
    border-radius:12px;
    outline:none;
    font-size:15px;
    transition:.3s;
}
.containerModalLogin input:focus{
    border-color:#7EC8E3;
    box-shadow:0 0 8px rgba(126,200,227,.3);
}
.botaoEntrarModal{
    width:100%;
    border:none;
    padding:15px;
    border-radius:30px;
    background:#7EC8E3;
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}
.botaoEntrarModal:hover{
    background:#68B8D8;
}
.rodapeModal{
    margin-top:25px;
    text-align:center;
}
.rodapeModal a{
    text-decoration:none;
    color:#7EC8E3;
    font-weight:600;
}
.botaoFecharModal{
    position:absolute;
    right:20px;
    top:15px;
    font-size:30px;
    color:#888;
    cursor:pointer;
}
.botaoFecharModal:hover{
    color:#6F5B47;
}
@keyframes abrirModal{
    from{
        opacity:0;
        transform:translateY(-30px) scale(.95);
    }
    to{
        opacity:1;
        transform:translateY(0) scale(1);
    }
}
.usuarioHeader{
    display:flex;
    align-items:center;
    gap:12px;
}
.fotoPerfilTopo{
    width:48px;
    height:48px;
    border-radius:50%;
    object-fit:cover;
    cursor:pointer;
    border:3px solid #7EC8E3;
    transition:.3s;
}
.fotoPerfilTopo:hover{
    transform:scale(1.08);
}
.nomeUsuarioTopo{
    text-decoration:none;
    color:#6F5B47;
    font-weight:600;
    position:relative;
}
.nomeUsuarioTopo::after{
    content:"Seu perfil";
    position:absolute;
    left:50%;
    transform:translateX(-50%);
    top:130%;
    background:#444;
    color:white;
    padding:5px 10px;
    border-radius:8px;
    font-size:12px;
    opacity:0;
    transition:.25s;
    pointer-events:none;
    white-space:nowrap;
}
.nomeUsuarioTopo:hover::after{
    opacity:1;
}
.usuarioHeader{
    display:flex;
    align-items:center;
    gap:12px;
}
.dadosUsuarioTopo{
    display:flex;
    flex-direction:column;
    align-items:flex-start;
    gap:3px;
}
.nomeUsuarioTopo{
    text-decoration:none;
    color:#6F5B47;
    font-weight:600;
    font-size:15px;
}
.botaoSair{
    display:flex;
    align-items:center;
    gap:5px;
    text-decoration:none;
    color:#888;
    font-size:13px;
    font-weight:500;
    transition:.3s;
}
.botaoSair:hover{
    color:#7EC8E3;
}
.iconeLogout{
    font-size:17px;
    line-height:1;
    transform:rotate(180deg);
    display:inline-block;
}
.painelAdmin {
    background: #F7F1E8;
    padding: 45px 5%;
    border-bottom: 1px solid #E5D9C9;
}

.containerAdmin {
    max-width: 1300px;
    margin: auto;
}

.containerAdmin h2 {
    color: #6F5B47;
    font-size: 30px;
    margin-bottom: 10px;
}

.containerAdmin > p {
    color: #666;
    margin-bottom: 28px;
}

.opcoesAdmin {
    display: grid;
    grid-template-columns:
        repeat(auto-fit, minmax(220px, 1fr));
    gap: 22px;
}

.cardAdmin {
    background: white;
    border-radius: 16px;
    padding: 25px;
    text-decoration: none;
    box-shadow: 0 8px 20px rgba(0,0,0,.06);
    border-top: 4px solid #7EC8E3;
    transition: .3s;
}

.cardAdmin:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,.10);
}

.cardAdmin span {
    font-size: 32px;
}

.cardAdmin h3 {
    color: #6F5B47;
    margin: 12px 0 8px;
}

.cardAdmin p {
    color: #777;
    line-height: 1.6;
    font-size: 14px;
}

.usuarioHeader {
    display: flex;
    align-items: center;
    gap: 12px;
}

.fotoPerfilTopo {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    cursor: pointer;
    border: 3px solid #7EC8E3;
    transition: .3s;
}

.fotoPerfilTopo:hover {
    transform: scale(1.08);
}

.modalPerfilConteudo {
    position: relative;
    width: min(460px, 92vw);
    padding: 28px;
    background: #FFFDFB;
    border-radius: 22px;
    box-shadow: 0 20px 50px rgba(0,0,0,.25);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 18px;
}

.imagemPerfilGrande {
    width: min(300px, 70vw);
    height: min(300px, 70vw);
    object-fit: cover;
    border-radius: 50%;
    border: 5px solid #EAF7FC;
    background: #EAF7FC;
}

.botaoEditarFoto {
    position: absolute;
    top: 24px;
    right: 24px;
    width: 44px;
    height: 44px;
    border: 0;
    border-radius: 50%;
    background: #7EC8E3;
    color: white;
    font-size: 21px;
    cursor: pointer;
}

.opcoesFotoPerfil {
    display: none;
    width: 100%;
    gap: 10px;
    flex-direction: column;
}

.opcoesFotoPerfil.visivel {
    display: flex;
}

.botaoAcaoFoto {
    width: 100%;
    border: 0;
    border-radius: 12px;
    padding: 13px 16px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
}

.botaoTrocarFoto {
    background: #7EC8E3;
    color: white;
}

.botaoExcluirFoto {
    background: #F7E8E4;
    color: #9B493C;
}

.textoFotoPerfil {
    color: #6F5B47;
    font-weight: 500;
    text-align: center;
}

</style>
</head>
<body>
<header class="headerSite">
<div class="navbarPrincipal">
<div class="logoSite">
Sinalize<span>Amor</span>
</div>
<ul class="menuPrincipal">
<li><a href="sobre.php">Sobre</a></li>
<li><a href="cursos.php">Cursos</a></li>
<li><a href="comunidade.php">Comunidade</a></li>
<li><a href="contato.php">Contato</a></li>
</ul>

<?php if (!$logado): ?>
    <a href="#" class="botaoContato"
       onclick="abrirModalLogin(); return false;">
        Entrar
    </a>
<?php else: ?>
    <div class="usuarioHeader">
        <img
            src="<?php echo htmlspecialchars($fotoPerfilUrl, ENT_QUOTES, 'UTF-8'); ?>"
            class="fotoPerfilTopo"
            id="fotoPerfilTopo"
            alt="Foto de perfil"
            title="Visualizar foto de perfil"
        >

        <a href="perfil.php" class="nomeUsuarioTopo">
            <?php echo htmlspecialchars($nomeUsuario); ?>
        </a>

        <a href="../control/logout.php" class="botaoSair">
            <span class="iconeLogout">↪</span>
            Sair
        </a>
    </div>
<?php endif; ?>

</div>
<div class="sloganSite">
A Caminho da Inclusão
</div>
</header>
<?php if ($logado && $ehAdmin): ?>
<section class="painelAdmin">
    <div class="containerAdmin">
        <h2>⚙️ Painel Administrativo</h2>

        <p>
            Bem-vindo, administrador!
            Gerencie a plataforma Sinalize Amor por aqui.
        </p>

        <div class="opcoesAdmin">
            <a href="usuarios.php" class="cardAdmin">
                <span>👥</span>
                <h3>Usuários</h3>
                <p>Consultar e gerenciar contas cadastradas.</p>
            </a>

            <a href="cursos.php" class="cardAdmin">
                <span>📚</span>
                <h3>Cursos</h3>
                <p>Gerenciar cursos, módulos e aulas.</p>
            </a>

            <a href="comunidade.php" class="cardAdmin">
                <span>💬</span>
                <h3>Comunidade</h3>
                <p>Administrar publicações e interações.</p>
            </a>

            <a href="configuracoes.php" class="cardAdmin">
                <span>⚙️</span>
                <h3>Configurações</h3>
                <p>Gerenciar as opções da plataforma.</p>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>
<section class="heroInicio">
<div class="containerHero">
<div class="textoHero">
<h1 class="tituloHero">
Aprenda Libras de forma simples, acolhedora e acessível.
</h1>
<p class="descricaoHero">
Nosso objetivo é aproximar pessoas por meio da Língua Brasileira de Sinais, oferecendo conteúdos teóricos e práticos para tornar a comunicação mais inclusiva.
</p>
<?php if ($logado): ?>
<a href="cursos.php" class="botaoComecar">
    Começar Agora
</a>
<?php else: ?>
<a href="#" class="botaoComecar" onclick="abrirModalLogin(); return false;">
    Começar Agora
</a>
<?php endif; ?>
</div>
<div class="imagemHero">
<img src="../style/imagem1.jpg" alt="Ilustração Libras">
</div>
</div>
</section>
<section class="containerCards">
<h2 class="tituloSessao">
Como você irá aprender?
</h2>
<p class="subtituloSessao">
Uma plataforma criada para ensinar Libras de maneira intuitiva e dinâmica.
</p>
<div class="cards">
<div class="cardTeoria">
<h2>📚 Conhecimento Teórico</h2>
<p>
Explore a parte teórica da Libras de forma simples e dinâmica. Aprenda os conceitos fundamentais, a estrutura da língua e a importância da comunicação inclusiva, criando uma base sólida para colocar o aprendizado em prática.
</p>
</div>
<div class="cardPratica">
<h2>🎥 Conhecimento Prático</h2>
<p>
Desenvolva sua comunicação em Libras assistindo a vídeos demonstrativos que ensinam os sinais de forma clara e objetiva, facilitando o aprendizado e a memorização.
</p>
</div>
<div class="cardInclusao">
<h2>🤝 Exercicíos Constantes</h2>
<p>
Ao final de cada aula, você terá acesso a exercícios desenvolvidos para reforçar o conteúdo aprendido. Coloque seus conhecimentos em prática, teste sua compreensão e acompanhe sua evolução por meio de atividades que facilitam a fixação dos sinais e conceitos da Libras.
</p>
</div>
</div>
</section>
<section class="secaoSobre">
    <div class="containerSobre">
        <div class="textoSobre">
            <h2>Por que aprender Libras?</h2>
            <p>
                A Língua Brasileira de Sinais é muito mais do que uma forma de comunicação. Ela representa identidade, cultura e inclusão. Ao aprender Libras, você contribui para uma sociedade mais acessível, amplia suas oportunidades pessoais e profissionais e fortalece a conexão com a comunidade surda.
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
            <input
                type="email"
                name="email"
                placeholder="E-mail"
                required
            >
            <input
                type="password"
                name="senha"
                placeholder="Senha"
                required
            >
            <button type="submit" class="botaoEntrarModal">
                Entrar
            </button>
        </form>
        <div class="rodapeModal">
            <p>
                Ainda não possui uma conta?
            <a href="#" onclick="abrirModalCadastro(); return false;">
    Criar conta
</a>
            </p>
        </div>
    </div>
</div>
<div id="modalCadastro" class="modalLogin">
    <div class="containerModalLogin">
        <span class="botaoFecharModal" onclick="fecharModalCadastro()">&times;</span>
        <h2>Criar conta</h2>
        <p>
            Preencha os dados para começar.
        </p>
        <form action="/tcc/control/cadastro.php" method="POST">
<input type="hidden" name="origem" value="inicio">
            <input
                type="text"
                name="nome"
                placeholder="Nome"
                required
            >
            <input
                type="email"
                name="email"
                placeholder="E-mail"
                required
            >
            <input
                type="password"
                name="senha"
                placeholder="Senha"
                required
            >
            <input
                type="password"
                name="confirmarSenha"
                placeholder="Confirmar senha"
                required
            >
            <button
                type="submit"
                class="botaoEntrarModal">
                Criar conta
            </button>
        </form>
        <div class="rodapeModal">
            <p>
                Já possui uma conta?
                <a href="#" onclick="abrirModalLogin(); fecharModalCadastro(); return false;">
                    Entrar
                </a>
            </p>
        </div>
    </div>
</div>
<div id="modalFotoPerfil" class="modalLogin" aria-hidden="true">
    <div class="modalPerfilConteudo"
         role="dialog"
         aria-modal="true"
         aria-label="Gerenciar foto de perfil">

        <button type="button"
                class="botaoFecharModal"
                onclick="fecharFotoPerfil()"
                aria-label="Fechar">
            &times;
        </button>

        <img
            id="imagemPerfilGrande"
            class="imagemPerfilGrande"
            src="<?php echo htmlspecialchars($fotoPerfilUrl, ENT_QUOTES, 'UTF-8'); ?>"
            alt="Foto de perfil ampliada"
        >

        <p class="textoFotoPerfil">Sua foto de perfil</p>

        <button type="button"
                class="botaoEditarFoto"
                onclick="alternarOpcoesFoto()"
                title="Editar foto"
                aria-label="Editar foto">
            ✎
        </button>

        <div id="opcoesFotoPerfil" class="opcoesFotoPerfil">

            <form id="formTrocarFoto"
                  action="../control/foto_perfil.php"
                  method="POST"
                  enctype="multipart/form-data">

                <input
                    type="file"
                    id="arquivoFotoPerfil"
                    name="foto_perfil"
                    accept="image/jpeg,image/png,image/webp"
                    hidden
                    required
                >

                <button type="button"
                        class="botaoAcaoFoto botaoTrocarFoto"
                        onclick="document.getElementById('arquivoFotoPerfil').click()">
                    Trocar foto
                </button>
            </form>

            <form action="../control/excluir_foto_perfil.php"
                  method="POST"
                  onsubmit="return confirm('Deseja excluir sua foto de perfil?');">

                <button type="submit"
                        class="botaoAcaoFoto botaoExcluirFoto">
                    Excluir foto
                </button>
            </form>

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
const fotoPerfilTopo = document.getElementById("fotoPerfilTopo");
const modalFotoPerfil = document.getElementById("modalFotoPerfil");
const opcoesFotoPerfil = document.getElementById("opcoesFotoPerfil");
const arquivoFotoPerfil = document.getElementById("arquivoFotoPerfil");
const imagemPerfilGrande = document.getElementById("imagemPerfilGrande");

if (fotoPerfilTopo && modalFotoPerfil && imagemPerfilGrande) {
    fotoPerfilTopo.addEventListener("click", function () {
        imagemPerfilGrande.src = this.src;

        if (opcoesFotoPerfil) {
            opcoesFotoPerfil.classList.remove("visivel");
        }

        modalFotoPerfil.style.display = "flex";
        modalFotoPerfil.setAttribute("aria-hidden", "false");
    });
}

function alternarOpcoesFoto() {
    if (opcoesFotoPerfil) {
        opcoesFotoPerfil.classList.toggle("visivel");
    }
}

function fecharFotoPerfil() {
    if (modalFotoPerfil) {
        modalFotoPerfil.style.display = "none";
        modalFotoPerfil.setAttribute("aria-hidden", "true");
    }

    if (opcoesFotoPerfil) {
        opcoesFotoPerfil.classList.remove("visivel");
    }
}

if (arquivoFotoPerfil) {
    arquivoFotoPerfil.addEventListener("change", function () {
        if (this.files && this.files.length > 0) {
            document.getElementById("formTrocarFoto").submit();
        }
    });
}

if (modalFotoPerfil) {
    modalFotoPerfil.addEventListener("click", function (event) {
        if (event.target === modalFotoPerfil) {
            fecharFotoPerfil();
        }
    });
}

document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
        fecharFotoPerfil();
    }
});

</script>
</body>
</html>