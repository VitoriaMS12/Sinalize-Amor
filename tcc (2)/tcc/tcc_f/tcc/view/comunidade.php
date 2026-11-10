<?php
session_start();
require_once __DIR__ . '/../model/conexao.php';

function h($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function fotoPerfil($arquivo): string {
    $nome = basename((string)$arquivo);
    if ($nome !== '' && preg_match('/^[a-zA-Z0-9_.-]+$/', $nome) && is_file(__DIR__ . '/../uploads/perfis/' . $nome)) {
        return '../uploads/perfis/' . rawurlencode($nome);
    }
    return '../style/usuario-padrao.svg';
}
function fotoPost($arquivo): ?string {
    $nome = basename((string)$arquivo);
    if ($nome === '' || !preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$/i', $nome)) return null;
    return is_file(__DIR__ . '/../uploads/publicacoes/' . $nome) ? '../uploads/publicacoes/' . rawurlencode($nome) : null;
}
function redirecionar($id = 0, $ok = '1'): void {
    header('Location: comunidade.php?ok=' . urlencode($ok) . ($id ? '#post-' . (int)$id : ''));
    exit;
}
function receberImagem(array $arquivo): string {
    if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new RuntimeException('Falha no envio da imagem.');
    if (($arquivo['size'] ?? 0) < 1 || $arquivo['size'] > 5 * 1024 * 1024) throw new RuntimeException('A imagem deve ter no máximo 5 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($tipos[$mime]) || @getimagesize($arquivo['tmp_name']) === false) throw new RuntimeException('Envie uma imagem JPG, PNG ou WebP válida.');
    $pasta = __DIR__ . '/../uploads/publicacoes/';
    if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) throw new RuntimeException('Não foi possível criar a pasta de imagens.');
    $nome = bin2hex(random_bytes(16)) . '.' . $tipos[$mime];
    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . $nome)) throw new RuntimeException('Não foi possível salvar a imagem.');
    return $nome;
}
function apagarImagem($nome): void {
    $arquivo = basename((string)$nome);
    if ($arquivo && preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $arquivo)) {
        $caminho = __DIR__ . '/../uploads/publicacoes/' . $arquivo;
        if (is_file($caminho)) @unlink($caminho);
    }
}

$logado = isset($_SESSION['id_usuario']);
$idUsuario = $logado ? (int)$_SESSION['id_usuario'] : 0;
$ehAdmin = false;
$nomeUsuario = 'Visitante';
$fotoUsuario = fotoPerfil(null);
if ($logado) {
    $st = $pdo->prepare('SELECT nome, foto_perfil, tipo_usuario, ativo FROM usuario WHERE id_usuario = ? LIMIT 1');
    $st->execute([$idUsuario]);
    $u = $st->fetch(PDO::FETCH_ASSOC);
    if (!$u || !(int)$u['ativo']) {
        unset($_SESSION['id_usuario'], $_SESSION['tipo_usuario']);
        $logado = false;
        $idUsuario = 0;
    } else {
        $ehAdmin = $u['tipo_usuario'] === 'administrador';
        $nomeUsuario = $u['nome'];
        $fotoUsuario = fotoPerfil($u['foto_perfil']);
    }
}
if (empty($_SESSION['csrf_comunidade'])) $_SESSION['csrf_comunidade'] = bin2hex(random_bytes(32));
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$logado) { http_response_code(403); exit('Faça login para continuar.'); }
    if (!hash_equals($_SESSION['csrf_comunidade'], (string)($_POST['csrf'] ?? ''))) { http_response_code(403); exit('Formulário expirado. Atualize a página.'); }
    $acao = (string)($_POST['acao'] ?? '');
    $idPost = filter_var($_POST['id_post'] ?? null, FILTER_VALIDATE_INT);
    try {
        if ($acao === 'criar' || $acao === 'editar') {
            $texto = trim((string)($_POST['texto'] ?? ''));
            if (mb_strlen($texto) > 3000) throw new RuntimeException('O texto deve ter até 3.000 caracteres.');
            $temImagem = isset($_FILES['imagem']) && ($_FILES['imagem']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
            $remover = !empty($_POST['remover_imagem']);
            $nomeNovo = null;
            $imagensAntigas = [];
            if ($acao === 'editar') {
                if (!$idPost) throw new RuntimeException('Publicação inválida.');
                $st = $pdo->prepare('SELECT id_usuario FROM post WHERE id_post = ? AND ativo = 1');
                $st->execute([$idPost]);
                $autor = $st->fetchColumn();
                if ($autor === false || (int)$autor !== $idUsuario) throw new RuntimeException('Você só pode editar suas próprias publicações.');
                $st = $pdo->prepare("SELECT arquivo FROM post_midia WHERE id_post = ? AND tipo = 'imagem'");
                $st->execute([$idPost]);
                $imagensAntigas = $st->fetchAll(PDO::FETCH_COLUMN);
            }
            if ($texto === '' && !$temImagem && ($acao === 'criar' || $remover || !$imagensAntigas)) throw new RuntimeException('Escreva um texto ou escolha uma foto.');
            if ($temImagem) $nomeNovo = receberImagem($_FILES['imagem']);
            try {
                $pdo->beginTransaction();
                if ($acao === 'criar') {
                    $st = $pdo->prepare("INSERT INTO post (id_usuario, texto, privacidade, ativo) VALUES (?, ?, 'publico', 1)");
                    $st->execute([$idUsuario, $texto]);
                    $idPost = (int)$pdo->lastInsertId();
                } else {
                    $st = $pdo->prepare('UPDATE post SET texto = ?, editado = 1 WHERE id_post = ? AND id_usuario = ? AND ativo = 1');
                    $st->execute([$texto, $idPost, $idUsuario]);
                    if ($remover || $nomeNovo !== null) {
                        $st = $pdo->prepare("DELETE FROM post_midia WHERE id_post = ? AND tipo = 'imagem'");
                        $st->execute([$idPost]);
                    }
                }
                if ($nomeNovo !== null) {
                    $st = $pdo->prepare("INSERT INTO post_midia (id_post, arquivo, tipo, ordem) VALUES (?, ?, 'imagem', 1)");
                    $st->execute([$idPost, $nomeNovo]);
                }
                $pdo->commit();
                if ($acao === 'editar' && ($remover || $nomeNovo !== null)) foreach ($imagensAntigas as $img) apagarImagem($img);
                redirecionar($idPost, 'publicado');
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                if ($nomeNovo !== null) apagarImagem($nomeNovo);
                throw $e;
            }
        } elseif ($acao === 'excluir') {
            if (!$idPost) throw new RuntimeException('Publicação inválida.');
            $st = $pdo->prepare('SELECT id_usuario FROM post WHERE id_post = ? AND ativo = 1');
            $st->execute([$idPost]);
            $autor = $st->fetchColumn();
            if ($autor === false || (!$ehAdmin && (int)$autor !== $idUsuario)) throw new RuntimeException('Sem permissão para excluir esta publicação.');
            $st = $pdo->prepare("SELECT arquivo FROM post_midia WHERE id_post = ? AND tipo = 'imagem'");
            $st->execute([$idPost]);
            $imagens = $st->fetchAll(PDO::FETCH_COLUMN);
            $st = $pdo->prepare('DELETE FROM post WHERE id_post = ?');
            $st->execute([$idPost]);
            foreach ($imagens as $img) apagarImagem($img);
            redirecionar(0, 'excluido');
        } elseif ($acao === 'curtir') {
            if (!$idPost) throw new RuntimeException('Publicação inválida.');
            $st = $pdo->prepare("SELECT id_post FROM post WHERE id_post = ? AND ativo = 1 AND privacidade = 'publico'");
            $st->execute([$idPost]);
            if (!$st->fetchColumn()) throw new RuntimeException('Publicação indisponível.');
            $st = $pdo->prepare('SELECT id_curtida FROM curtida WHERE id_post = ? AND id_usuario = ?');
            $st->execute([$idPost, $idUsuario]);
            if ($st->fetchColumn()) {
                $st = $pdo->prepare('DELETE FROM curtida WHERE id_post = ? AND id_usuario = ?');
            } else {
                $st = $pdo->prepare('INSERT IGNORE INTO curtida (id_post, id_usuario) VALUES (?, ?)');
            }
            $st->execute([$idPost, $idUsuario]);
            redirecionar($idPost, 'curtida');
        } elseif ($acao === 'comentar') {
            $comentario = trim((string)($_POST['comentario'] ?? ''));
            if (!$idPost || $comentario === '' || mb_strlen($comentario) > 1000) throw new RuntimeException('Comentário inválido (máximo de 1.000 caracteres).');
            $st = $pdo->prepare("INSERT INTO comentario (id_post, id_usuario, comentario, ativo) SELECT id_post, ?, ?, 1 FROM post WHERE id_post = ? AND ativo = 1 AND privacidade = 'publico'");
            $st->execute([$idUsuario, $comentario, $idPost]);
            if (!$st->rowCount()) throw new RuntimeException('Publicação indisponível.');
            redirecionar($idPost, 'comentario');
        } elseif ($acao === 'excluir_comentario') {
            $idComentario = filter_var($_POST['id_comentario'] ?? null, FILTER_VALIDATE_INT);
            if (!$idComentario) throw new RuntimeException('Comentário inválido.');
            $st = $pdo->prepare('SELECT id_usuario, id_post FROM comentario WHERE id_comentario = ?');
            $st->execute([$idComentario]);
            $c = $st->fetch(PDO::FETCH_ASSOC);
            if (!$c || (!$ehAdmin && (int)$c['id_usuario'] !== $idUsuario)) throw new RuntimeException('Sem permissão para excluir este comentário.');
            $st = $pdo->prepare('DELETE FROM comentario WHERE id_comentario = ?');
            $st->execute([$idComentario]);
            redirecionar((int)$c['id_post'], 'comentario_excluido');
        } else {
            throw new RuntimeException('Ação inválida.');
        }
    } catch (Throwable $e) {
        error_log('Comunidade: ' . $e->getMessage());
        $erro = $e instanceof RuntimeException ? $e->getMessage() : 'Não foi possível concluir a operação. Verifique o banco de dados.';
    }
}

$editarPost = null;
if ($logado && isset($_GET['editar'])) {
    $idEditar = filter_var($_GET['editar'], FILTER_VALIDATE_INT);
    if ($idEditar) {
        $st = $pdo->prepare('SELECT id_post, texto FROM post WHERE id_post = ? AND id_usuario = ? AND ativo = 1');
        $st->execute([$idEditar, $idUsuario]);
        $editarPost = $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

$st = $pdo->prepare("SELECT p.id_post, p.id_usuario, p.texto, p.editado, p.data_postagem, u.nome, u.sobrenome, u.username, u.foto_perfil,
    (SELECT m.arquivo FROM post_midia m WHERE m.id_post = p.id_post AND m.tipo = 'imagem' ORDER BY m.ordem, m.id_midia LIMIT 1) AS imagem,
    (SELECT COUNT(*) FROM curtida c WHERE c.id_post = p.id_post) AS total_curtidas,
    (SELECT COUNT(*) FROM comentario c WHERE c.id_post = p.id_post AND c.ativo = 1) AS total_comentarios
    FROM post p JOIN usuario u ON u.id_usuario = p.id_usuario
    WHERE p.ativo = 1 AND p.privacidade = 'publico' ORDER BY p.data_postagem DESC, p.id_post DESC LIMIT 100");
$st->execute();
$posts = $st->fetchAll(PDO::FETCH_ASSOC);

$curtidos = [];
if ($logado) {
    $st = $pdo->prepare('SELECT id_post FROM curtida WHERE id_usuario = ?');
    $st->execute([$idUsuario]);
    $curtidos = array_fill_keys($st->fetchAll(PDO::FETCH_COLUMN), true);
}
$comentariosPorPost = [];
if ($posts) {
    $ids = array_column($posts, 'id_post');
    $marcadores = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare("SELECT c.id_comentario, c.id_post, c.id_usuario, c.comentario, c.data_comentario, u.nome, u.sobrenome, u.foto_perfil
        FROM comentario c JOIN usuario u ON u.id_usuario = c.id_usuario
        WHERE c.ativo = 1 AND c.id_post IN ($marcadores) ORDER BY c.data_comentario, c.id_comentario");
    $st->execute($ids);
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $c) $comentariosPorPost[$c['id_post']][] = $c;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Comunidade | Sinalize Amor</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {
    box-sizing:border-box}
body {
    margin:0;
    background:#FDFBF8;
    color:#555;
    font-family:Poppins,sans-serif}
button,input,textarea {
    font:inherit}
a {
    text-decoration:none;
    color:inherit}
.topo {
    background:#EAF7FC;
    box-shadow:0 3px 12px #0001}
.nav {
    max-width:1180px;
    margin:auto;
    padding:18px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    flex-wrap:wrap}
.logo {
    font-size:29px;
    font-weight:700;
    color:#6F5B47}
.logo span {
    color:#7EC8E3}
.links {
    display:flex;
    gap:20px;
    flex-wrap:wrap}
.links a {
    color:#6F5B47}
.perfil {
    display:flex;
    align-items:center;
    gap:10px}
.perfil img,.avatar {
    width:45px;
    height:45px;
    border-radius:50%;
    object-fit:cover}
.botao {
    background:#7EC8E3;
    color:white;
    border:0;
    border-radius:25px;
    padding:11px 20px;
    cursor:pointer;
    font-weight:600;
    display:inline-block}
.botao:hover {
    background:#5FB4D3}
.botao.sec {
    background:#A68A64}
.botao.perigo {
    background:#D97676}
.botao.pequeno {
    font-size:12px;
    padding:7px 12px}
.hero {
    text-align:center;
    padding:45px 20px 65px;
    background:linear-gradient(130deg,#EAF7FC,#F7F1E8)}
.hero h1 {
    color:#6F5B47;
    font-size:40px;
    margin:0 0 10px}
.hero p {
    max-width:720px;
    margin:auto;
    line-height:1.8}
.layout {
    max-width:1150px;
    margin:-30px auto 70px;
    padding:0 18px;
    display:grid;
    grid-template-columns:minmax(0,1fr)270px;
    gap:22px;
    align-items:start}
.card {
    background:#fff;
    border-radius:18px;
    box-shadow:0 8px 25px #0000000d;
    padding:24px;
    margin-bottom:20px}
.card h2,.card h3 {
    color:#6F5B47;
    margin-top:0}
.identidade {
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:15px}
.identidade strong {
    color:#6F5B47}
.data {
    font-size:12px;
    color:#999}
.campo,textarea {
    width:100%;
    padding:13px;
    border:1px solid #DDE5E8;
    border-radius:12px;
    outline:none}
textarea {
    min-height:115px;
    resize:vertical}
.campo:focus,textarea:focus {
    border-color:#7EC8E3}
.linha {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin-top:12px}
.fotoPost {
    width:100%;
    max-height:520px;
    object-fit:contain;
    border-radius:12px;
    margin:10px 0}
.textoPost {
    white-space:pre-wrap;
    overflow-wrap:anywhere;
    line-height:1.8}
.acoes {
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
    margin:14px 0}
.acoes form {
    margin:0}
.comentario {
    display:flex;
    gap:10px;
    background:#F8F8F6;
    border-radius:12px;
    padding:10px;
    margin:10px 0}
.comentario img {
    width:34px;
    height:34px;
    border-radius:50%;
    object-fit:cover}
.comentario p {
    margin:4px 0;
    white-space:pre-wrap;
    overflow-wrap:anywhere}
.comentario .corpo {
    flex:1;
    min-width:0}
.formComentario {
    display:flex;
    gap:8px;
    margin-top:12px}
.formComentario input {
    flex:1;
    min-width:0}
.aviso {
    padding:12px 16px;
    border-radius:10px;
    background:#E7F7EF;
    color:#26764B;
    margin-bottom:15px}
.erro {
    background:#FBE9E5;
    color:#914A3F}
.lateral {
    position:sticky;
    top:20px}
.lateral p,.lateral li {
    font-size:13px;
    line-height:1.8}
.modal {
    display:none;
    position:fixed;
    inset:0;
    background:#0008;
    z-index:100;
    align-items:center;
    justify-content:center;
    padding:20px}
.modal.aberto {
    display:flex}
.janela {
    width:100%;
    max-width:420px;
    background:white;
    border-radius:20px;
    padding:30px;
    position:relative}
.janela h2 {
    color:#6F5B47}
.janela .campo {
    margin-bottom:12px}
.fechar {
    position:absolute;
    right:17px;
    top:10px;
    background:none;
    border:0;
    font-size:28px;
    cursor:pointer}
.rodape {
    text-align:center;
    padding:25px;
    background:#F7F1E8;
    color:#6F5B47}
@media(max-width:800px) {
    .layout {
    grid-template-columns:1fr}
.lateral {
    position:static}
.hero h1 {
    font-size:31px}
}
@media(max-width:480px) {
    .card {
    padding:17px}
.formComentario {
    flex-direction:column}
}
</style>
</head>
<body>
<header class="topo">
<nav class="nav">
<a class="logo" href="inicio.php">Sinalize<span>Amor</span>
</a>
<div class="links">
<a href="inicio.php">Início</a>
<a href="sobre.php">Sobre</a>
<a href="cursos.php">Cursos</a>
<a href="contato.php">Contato</a>
</div>
<?php if ($logado): ?>
<div class="perfil">
<img src="<?= h($fotoUsuario) ?>" alt="">
<a href="perfil.php"><?= h($nomeUsuario) ?>
</a>
<a href="../control/logout.php">Sair ↪</a>
</div>
<?php else: ?>
<button type="button" class="botao" onclick="abrirLogin()">Entrar</button>
<?php endif; ?>
</nav>
</header>
<section class="hero">
<h1>💙 Nossa comunidade</h1>
<p>Compartilhe experiências, fotos, descobertas e dúvidas sobre Libras. Um espaço para aprender e crescer juntos.</p>
</section>
<main class="layout">
<section>
<?php if ($erro): ?>
<div class="aviso erro"><?= h($erro) ?>
</div>
<?php endif; ?>
<?php if (isset($_GET['ok'])): ?>
<div class="aviso">Operação realizada com sucesso!</div>
<?php endif; ?>
<div class="card" id="publicar">
<h2><?= $editarPost ? 'Editar publicação' : 'Compartilhe com a comunidade' ?>
</h2>
<?php if ($logado): ?>
<div class="identidade">
<img class="avatar" src="<?= h($fotoUsuario) ?>" alt="">
<strong><?= h($nomeUsuario) ?>
</strong>
</div>
<form method="POST" enctype="multipart/form-data" action="comunidade.php<?= $editarPost ? '?editar=' . (int)$editarPost['id_post'] : '' ?>">
<input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_comunidade']) ?>">
<input type="hidden" name="acao" value="<?= $editarPost ? 'editar' : 'criar' ?>">
<?php if ($editarPost): ?>
<input type="hidden" name="id_post" value="<?= (int)$editarPost['id_post'] ?>"><?php endif; ?>
<textarea name="texto" maxlength="3000" placeholder="O que você gostaria de compartilhar?"><?= h($editarPost['texto'] ?? '') ?>
</textarea>
<div class="linha">
<label>📷 Foto (JPG, PNG ou WebP, até 5 MB)<input class="campo" type="file" name="imagem" accept="image/jpeg,image/png,image/webp">
</label>
</div>
<?php if ($editarPost): ?>
<label>
<input type="checkbox" name="remover_imagem" value="1"> Remover foto atual (caso exista)</label>
<?php endif; ?>
<div class="linha">
<small>Respeite todas as pessoas 💙</small>
<div>
<button class="botao" type="submit"><?= $editarPost ? 'Salvar alterações' : 'Publicar ✨' ?>
</button>
<?php if ($editarPost): ?>
<a class="botao sec" href="comunidade.php">Cancelar</a>
<?php endif; ?>
</div>
</div>
</form>
<?php else: ?>
<p>Entre na sua conta para publicar fotos, curtir e comentar.</p>
<button class="botao" type="button" onclick="abrirLogin()">Entrar agora</button>
<?php endif; ?>
</div>
<?php if (!$posts): ?>
<div class="card">
<h3>🌱 A comunidade está começando!</h3>
<p>Seja a primeira pessoa a publicar.</p>
</div>
<?php endif; ?>
<?php foreach ($posts as $post): ?>
<article class="card" id="post-<?= (int)$post['id_post'] ?>">
<div class="identidade">
<img class="avatar" src="<?= h(fotoPerfil($post['foto_perfil'])) ?>" alt="">
<div>
<strong><?= h(trim($post['nome'] . ' ' . ($post['sobrenome'] ?? ''))) ?>
</strong>
<div class="data"><?= h(date('d/m/Y H:i', strtotime($post['data_postagem']))) ?>
<?= $post['editado'] ? ' · editado' : '' ?>
</div>
</div>
</div>
<?php if ($post['texto'] !== ''): ?>
<p class="textoPost"><?= h($post['texto']) ?>
</p>
<?php endif; ?>
<?php $imagemUrl = fotoPost($post['imagem']); if ($imagemUrl): ?>
<img class="fotoPost" src="<?= h($imagemUrl) ?>" alt="Foto compartilhada na comunidade" loading="lazy"><?php endif; ?>
<div class="data">❤️ <?= (int)$post['total_curtidas'] ?> curtidas · 💬 <?= (int)$post['total_comentarios'] ?> comentários</div>
<div class="acoes">
<?php if ($logado): ?>
<form method="POST">
<input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_comunidade']) ?>">
<input type="hidden" name="acao" value="curtir">
<input type="hidden" name="id_post" value="<?= (int)$post['id_post'] ?>">
<button class="botao pequeno" type="submit"><?= isset($curtidos[$post['id_post']]) ? '❤️ Curtido' : '♡ Curtir' ?>
</button>
</form>
<?php if ((int)$post['id_usuario'] === $idUsuario): ?>
<a class="botao sec pequeno" href="comunidade.php?editar=<?= (int)$post['id_post'] ?>#publicar">✏️ Editar</a>
<?php endif; ?>
<?php if ((int)$post['id_usuario'] === $idUsuario || $ehAdmin): ?>
<form method="POST" onsubmit="return confirm('Excluir esta publicação e seus comentários?')">
<input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_comunidade']) ?>">
<input type="hidden" name="acao" value="excluir">
<input type="hidden" name="id_post" value="<?= (int)$post['id_post'] ?>">
<button class="botao perigo pequeno" type="submit">🗑️ Excluir</button>
</form>
<?php endif; ?>
<?php else: ?>
<button class="botao pequeno" type="button" onclick="abrirLogin()">♡ Curtir</button>
<?php endif; ?>
</div>
<div class="comentarios">
<h3>Comentários</h3>
<?php foreach ($comentariosPorPost[$post['id_post']] ?? [] as $coment): ?>
<div class="comentario">
<img src="<?= h(fotoPerfil($coment['foto_perfil'])) ?>" alt="">
<div class="corpo">
<strong><?= h(trim($coment['nome'] . ' ' . ($coment['sobrenome'] ?? ''))) ?>
</strong>
<p><?= h($coment['comentario']) ?>
</p>
<span class="data"><?= h(date('d/m/Y H:i', strtotime($coment['data_comentario']))) ?>
</span>
<?php if ($logado && ($ehAdmin || (int)$coment['id_usuario'] === $idUsuario)): ?>
<form method="POST" onsubmit="return confirm('Excluir comentário?')">
<input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_comunidade']) ?>">
<input type="hidden" name="acao" value="excluir_comentario">
<input type="hidden" name="id_comentario" value="<?= (int)$coment['id_comentario'] ?>">
<button class="botao perigo pequeno" type="submit">Excluir</button>
</form>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>
<?php if ($logado): ?>
<form class="formComentario" method="POST">
<input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_comunidade']) ?>">
<input type="hidden" name="acao" value="comentar">
<input type="hidden" name="id_post" value="<?= (int)$post['id_post'] ?>">
<input class="campo" type="text" name="comentario" maxlength="1000" placeholder="Escreva um comentário..." required>
<button class="botao" type="submit">Enviar</button>
</form>
<?php else: ?>
<button class="botao pequeno" type="button" onclick="abrirLogin()">Entre para comentar</button>
<?php endif; ?>
</div>
</article>
<?php endforeach; ?>
</section>
<aside class="lateral">
<div class="card">
<h3>🤟 Nosso propósito</h3>
<p>Conectar pessoas e incentivar o aprendizado da Língua Brasileira de Sinais.</p>
<h3>Combinados</h3>
<ul>
<li>Respeite as diferenças.</li>
<li>Compartilhe com responsabilidade.</li>
<li>Ajude quem está aprendendo.</li>
</ul>
</div>
<div class="card">
<h3>💡 Ideias para publicar</h3>
<p>Um sinal novo, uma dúvida, uma conquista ou uma foto dos seus estudos.</p>
</div>
</aside>
</main>
<footer class="rodape">SinalizeAmor — A Caminho da Inclusão · <?= date('Y') ?>
</footer>
<?php if (!$logado): ?>
<div class="modal" id="modalLogin" role="dialog" aria-modal="true" aria-labelledby="tituloLogin">
<div class="janela">
<button class="fechar" type="button" onclick="fecharLogin()" aria-label="Fechar">×</button>
<h2 id="tituloLogin">Entrar no Sinalize Amor</h2>
<p>Faça login para participar da comunidade.</p>
<form method="POST" action="../control/login.php">
<input class="campo" type="email" name="email" placeholder="Seu e-mail" required>
<input class="campo" type="password" name="senha" placeholder="Sua senha" required>
<button class="botao" style="width:100%" type="submit">Entrar</button>
</form>
<p>Ainda não tem conta? <a href="cadastro.php" style="color:#2397BD">Criar conta</a>
</p>
</div>
</div>
<script>
function abrirLogin(){document.getElementById('modalLogin').classList.add('aberto')}
function fecharLogin(){document.getElementById('modalLogin').classList.remove('aberto')}
document.getElementById('modalLogin').addEventListener('click',e=>{if(e.target.id==='modalLogin')fecharLogin()});
document.addEventListener('keydown',e=>{if(e.key==='Escape')fecharLogin()});
</script>
<?php endif; ?>
</body>
</html>
