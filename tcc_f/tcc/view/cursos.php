<?php
session_start();
require_once __DIR__ . "/../model/conexao.php";

$logado = isset($_SESSION["id_usuario"]);
$nomeUsuario = $_SESSION["nome"] ?? "";
$ehAdmin = false;

// Consulta a permissão atual no banco, não apenas na sessão.
if ($logado) {
    $consultaUsuario = $pdo->prepare("SELECT tipo_usuario, ativo FROM usuario WHERE id_usuario = ? LIMIT 1");
    $consultaUsuario->execute([$_SESSION["id_usuario"]]);
    $dadosUsuario = $consultaUsuario->fetch(PDO::FETCH_ASSOC);
    if (!$dadosUsuario || !(bool)$dadosUsuario["ativo"]) {
        $logado = false;
        $nomeUsuario = "";
    } else {
        $ehAdmin = ($dadosUsuario["tipo_usuario"] === "administrador");
    }
}

// Os módulos são fixos e já estão cadastrados no MySQL.
$stmt = $pdo->prepare("
    SELECT m.id_modulo, m.titulo, m.subtitulo, m.descricao, m.icone, m.ordem
    FROM modulo m
    INNER JOIN curso c ON c.id_curso = m.id_curso
    WHERE c.titulo = :titulo AND c.ativo = 1 AND m.ativo = 1
    ORDER BY m.ordem ASC, m.id_modulo ASC
");
$stmt->execute(["titulo" => "Curso de Libras"]);
$modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Administradores veem rascunhos; alunos só veem aulas publicadas.
$sqlAulas = "SELECT a.id_aula, a.id_modulo, a.titulo, a.publicada
             FROM aula a
             WHERE a.ativo = 1";
if (!$ehAdmin) {
    $sqlAulas .= " AND a.publicada = 1";
}
$sqlAulas .= " ORDER BY a.ordem ASC, a.id_aula ASC";
$stmtAulas = $pdo->query($sqlAulas);
$aulasPorModulo = [];
foreach ($stmtAulas->fetchAll(PDO::FETCH_ASSOC) as $aula) {
    $aulasPorModulo[$aula["id_modulo"]][] = $aula;
}

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cursos - Sinalize Amor</title>
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
.menuPrincipal a{
    text-decoration:none;
    color:#6F5B47;
    font-weight:500;
    transition:.3s;
}
.menuPrincipal a:hover{
    color:#7EC8E3;
}
.botaoVoltar{
    background:#7EC8E3;
    color:white;
    padding:12px 24px;
    border-radius:30px;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}
.botaoVoltar:hover{
    background:#68B8D8;
    transform:translateY(-2px);
}
.sloganSite{
    text-align:center;
    padding-bottom:15px;
    color:#A68A64;
    font-style:italic;
}
.introducaoCursos{
    width:90%;
    max-width:1000px;
    margin:60px auto;
    text-align:center;
}
.nivelUsuario{
    display:inline-block;
    background:#7EC8E3;
    color:white;
    padding:12px 35px;
    border-radius:30px;
    font-size:18px;
    font-weight:600;
    margin-bottom:30px;
    box-shadow:0 5px 15px rgba(126,200,227,.25);
}
.introducaoCursos h1{
    color:#6F5B47;
    font-size:42px;
    margin-bottom:20px;
}
.introducaoCursos p{
    font-size:18px;
    line-height:1.8;
    color:#777;
}
.containerModulos{
    width:90%;
    max-width:1200px;
    margin:auto;
    padding-bottom:30px;
}
.tituloModulos{
    text-align:center;
    color:#6F5B47;
    font-size:34px;
    margin-bottom:40px;
}
.listaModulos{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:30px;
    align-items:start;
}
.cardModulo{
    background:white;
    border-radius:22px;
    padding:30px;
    min-height:240px;
    cursor:pointer;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    border-top:6px solid #7EC8E3;
    transition:.4s ease;
    position:relative;
    overflow:visible;
    z-index:1;
}
.cardModulo:hover{
    transform:scale(1.05);
    z-index:10;
    box-shadow:0 20px 45px rgba(0,0,0,.18);
}
.cardModulo h2{
    color:#6F5B47;
    margin-bottom:15px;
}
.nivelModulo{
    color:#7EC8E3;
    font-weight:600;
    display:block;
    margin-bottom:15px;
}
.resumoModulo{
    color:#777;
    line-height:1.6;
}
.conteudoModulo{
    max-height:0;
    opacity:0;
    overflow:hidden;
    transition:.5s ease;
}
.cardModulo:hover .conteudoModulo{
    max-height:500px;
    opacity:1;
    margin-top:25px;
}
.conteudoModulo p{
    line-height:1.7;
    color:#666;
}
.botaoComecarModulo{
    display:inline-block;
    margin-top:25px;
    padding:13px 30px;
    background:#7EC8E3;
    color:white;
    text-decoration:none;
    border-radius:30px;
    font-weight:600;
    transition:.3s;
    position:relative;
    z-index:20;
}
.botaoComecarModulo:hover{
    background:#5FB4D3;
    transform:translateY(-2px);
}
footer{
    margin-top:80px;
    background:#F4EBDD;
    padding:30px;
    text-align:center;
    color:#6F5B47;
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
.conteudoLogin{
    width:420px;
    max-width:92%;
    background:#FFFDFB;
    border-radius:20px;
    padding:40px;
    position:relative;
    animation:abrirModal .35s ease;
    box-shadow:0 20px 50px rgba(0,0,0,.18);
}
.conteudoLogin h2{
    color:#6F5B47;
    text-align:center;
    margin-bottom:10px;
}
.conteudoLogin p{
    text-align:center;
    color:#777;
    margin-bottom:25px;
}
.conteudoLogin input{
    width:100%;
    padding:14px;
    margin-bottom:18px;
    border:1px solid #D7D7D7;
    border-radius:12px;
    outline:none;
    font-size:15px;
    transition:.3s;
}
.conteudoLogin input:focus{
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
.fechar{
    position:absolute;
    right:20px;
    top:15px;
    font-size:30px;
    color:#888;
    cursor:pointer;
}
.fechar:hover{
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
@media(max-width:1000px){
    .navbarPrincipal{
        flex-wrap:wrap;
        height:auto;
        padding:20px 0;
        gap:20px;
        justify-content:center;
    }
    .logoSite{
        width:100%;
        text-align:center;
    }
    .menuPrincipal{
        flex-wrap:wrap;
        justify-content:center;
        gap:20px;
    }
}
@media(max-width:700px){
    .introducaoCursos h1{
        font-size:34px;
    }
    .tituloModulos{
        font-size:28px;
    }
    .menuPrincipal{
        gap:15px;
    }
    .menuPrincipal a{
        font-size:14px;
    }
    .botaoVoltar{
        padding:10px 20px;
        font-size:14px;
    }
}
@media(max-width:500px){
    .logoSite{
        font-size:29px;
    }
    .menuPrincipal{
        gap:12px;
    }
    .menuPrincipal a{
        font-size:13px;
    }
    .introducaoCursos{
        margin:40px auto;
    }
    .introducaoCursos h1{
        font-size:29px;
    }
    .introducaoCursos p{
        font-size:16px;
    }
    .nivelUsuario{
        font-size:15px;
        padding:10px 20px;
    }
    .cardModulo{
        padding:25px;
    }
}

.cardModulo:focus-within .conteudoModulo{max-height:700px;opacity:1;margin-top:25px}
.listaAulas{list-style:none;margin-top:12px;padding:0}
.listaAulas li{padding:7px 0;border-bottom:1px solid #eee;color:#6F5B47}
.avisoVazio{font-size:14px;color:#888;margin-top:12px}
.botaoAdmin{display:inline-block;background:#A68A64;color:white;text-decoration:none;padding:11px 18px;border-radius:22px;font-weight:600;margin-top:15px}
.botaoAdmin:hover{background:#80684b}
</style>
</head>
<body>
<header class="headerSite"><div class="navbarPrincipal">
<div class="logoSite">Sinalize<span>Amor</span></div>
<ul class="menuPrincipal">
<li><a href="inicio.php">Início</a></li><li><a href="sobre.php">Sobre</a></li>
<li><a href="comunidade.php">Comunidade</a></li><li><a href="contato.php">Contato</a></li>
</ul><a href="inicio.php" class="botaoVoltar">Voltar</a>
</div><div class="sloganSite">A Caminho da Inclusão</div></header>
<section class="introducaoCursos"><div class="nivelUsuario">
<?php if (!$logado): ?>📚 Você está no nível 0 - Iniciante
<?php else: ?>👋 Olá, <?= escapar($nomeUsuario) ?>!<br>
📚 Seu nível: 0 - Iniciante<br><small>Você ainda não iniciou nenhum curso.</small>
<?php endif; ?></div>
<h1>Aprenda Libras do zero</h1>
<p>Nossa trilha de aprendizado foi criada para ensinar Libras de forma simples, interativa e progressiva. Você aprenderá desde os primeiros sinais até construir conversas mais completas utilizando vídeos, exercícios e práticas.</p>
</section>
<section class="containerModulos"><h2 class="tituloModulos">Trilha de Aprendizado</h2>
<div class="listaModulos">
<?php foreach ($modulos as $modulo): ?>
<div class="cardModulo">
<h2><?= escapar($modulo["icone"] ?: (str_starts_with($modulo["titulo"], "Intermediário") ? "💙" : "🤟")) ?> <?= escapar($modulo["titulo"]) ?></h2>
<span class="nivelModulo"><?= escapar($modulo["subtitulo"] ?: "Módulo " . $modulo["ordem"]) ?></span>
<p class="resumoModulo"><?= escapar($modulo["descricao"] ?: "Aprenda Libras no seu ritmo.") ?></p>
<div class="conteudoModulo"><p><strong>Aulas do módulo:</strong></p>
<?php $lista = $aulasPorModulo[$modulo["id_modulo"]] ?? []; ?>
<?php if ($lista): ?>
<ul class="listaAulas">
<?php foreach ($lista as $aula): ?>
<li><?= escapar($aula["titulo"]) ?><?php if ($ehAdmin && !$aula["publicada"]): ?> <small>(Rascunho)</small><?php endif; ?></li>
<?php endforeach; ?>
</ul>
<?php else: ?><p class="avisoVazio">Nenhuma aula <?= $ehAdmin ? "cadastrada" : "disponível" ?> neste módulo ainda.</p><?php endif; ?>
<?php if ($logado): ?>
<a href="modulo.php?id=<?= (int)$modulo["id_modulo"] ?>" class="botaoComecarModulo">Ver módulo</a>
<?php else: ?>
<a href="#" onclick="abrirLogin();return false;" class="botaoComecarModulo">Começar Módulo</a>
<?php endif; ?>
<?php if ($ehAdmin): ?>
<a href="modulo.php?id=<?= (int)$modulo["id_modulo"] ?>" class="botaoAdmin">⚙️ Gerenciar aulas</a>
<?php endif; ?>
</div></div>
<?php endforeach; ?>
<?php if (!$modulos): ?><p>Os módulos ainda não estão disponíveis.</p><?php endif; ?>
</div></section>
<footer>© <?= date("Y") ?> Sinalize Amor - Todos os direitos reservados.</footer>
<div id="modalLogin" class="modalLogin">
    <div class="conteudoLogin">
        <span onclick="fecharLogin()" class="fechar">
            ×
        </span>
        <h2>
            Entrar para continuar
        </h2>
        <p>
            Faça login para iniciar seu curso.
        </p>
        <form action="../control/login.php" method="POST">
            <input
                type="email"
                name="email"
                placeholder="Seu email"
                required
            >
            <input
                type="password"
                name="senha"
                placeholder="Sua senha"
                required
            >
            <button
                type="submit"
                class="botaoEntrarModal"
            >
                Entrar
            </button>
        </form>
        <p class="rodapeModal">
            Ainda não possui conta?
            <a href="inicio.php">
                Criar conta
            </a>
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