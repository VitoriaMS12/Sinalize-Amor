
<?php
session_start();
require_once "../model/conexao.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: inicio.php");
    exit;
}

function e($valor) {
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

$idUsuario = (int) $_SESSION["id_usuario"];
$idModulo = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$idModulo || $idModulo < 1) {
    header("Location: cursos.php");
    exit;
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

/* Verificar usuário e permissões diretamente no banco */
$stmt = $pdo->prepare("
    SELECT tipo_usuario, ativo
    FROM usuario
    WHERE id_usuario = ?
    LIMIT 1
");
$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || !(int)$usuario["ativo"]) {
    http_response_code(403);
    exit("Usuário sem permissão de acesso.");
}

$ehAdmin = $usuario["tipo_usuario"] === "administrador";

/* Buscar módulo */
$stmt = $pdo->prepare("
    SELECT id_modulo, titulo, descricao
    FROM modulo
    WHERE id_modulo = ?
      AND ativo = 1
    LIMIT 1
");
$stmt->execute([$idModulo]);
$modulo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$modulo) {
    http_response_code(404);
    exit("Módulo não encontrado.");
}

$urlModulo = "modulo.php?id=" . $idModulo;
$erro = "";
$mensagem = "";

/* Gerenciamento de aulas: somente administrador */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tokenRecebido = $_POST["csrf_token"] ?? "";

    if (!is_string($tokenRecebido) ||
        !hash_equals($_SESSION["csrf_token"], $tokenRecebido)) {
        http_response_code(403);
        exit("Formulário inválido. Atualize a página.");
    }

    $acao = $_POST["acao"] ?? "";
    $idAulaPost = filter_var(
        $_POST["id_aula"] ?? null,
        FILTER_VALIDATE_INT
    );

    if (in_array($acao, [
        "salvar", "excluir", "publicar"
    ], true)) {

        if (!$ehAdmin) {
            http_response_code(403);
            exit("Acesso negado.");
        }

        if ($acao === "salvar") {
            $titulo = trim($_POST["titulo"] ?? "");
            $descricao = trim($_POST["descricao"] ?? "");
            $conteudo = trim($_POST["conteudo"] ?? "");
            $video = trim($_POST["video"] ?? "");
            $ordem = filter_var(
                $_POST["ordem"] ?? 1,
                FILTER_VALIDATE_INT
            );
            $publicada = isset($_POST["publicada"]) ? 1 : 0;

            if ($titulo === "" || mb_strlen($titulo) > 150) {
                $erro = "Informe um título de até 150 caracteres.";
            } elseif ($ordem === false || $ordem < 1) {
                $erro = "Informe uma ordem válida.";
            } elseif (mb_strlen($video) > 255) {
                $erro = "O endereço do vídeo é muito longo.";
            } else {
                if ($idAulaPost) {
                    $stmt = $pdo->prepare("
                        UPDATE aula
                        SET titulo = ?,
                            descricao = ?,
                            conteudo = ?,
                            video = ?,
                            ordem = ?,
                            publicada = ?,
                            data_atualizacao = NOW()
                        WHERE id_aula = ?
                          AND id_modulo = ?
                    ");
                    $stmt->execute([
                        $titulo, $descricao, $conteudo,
                        $video, $ordem, $publicada,
                        $idAulaPost, $idModulo
                    ]);
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO aula (
                            id_modulo, titulo, descricao,
                            conteudo, video, ordem,
                            publicada, ativo
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)
                    ");
                    $stmt->execute([
                        $idModulo, $titulo, $descricao,
                        $conteudo, $video, $ordem, $publicada
                    ]);
                }

                header("Location: $urlModulo&sucesso=salva");
                exit;
            }
        }

        if ($acao === "publicar" && $idAulaPost) {
            $stmt = $pdo->prepare("
                UPDATE aula
                SET publicada = CASE
                        WHEN publicada = 1 THEN 0
                        ELSE 1
                    END,
                    data_atualizacao = NOW()
                WHERE id_aula = ?
                  AND id_modulo = ?
            ");
            $stmt->execute([$idAulaPost, $idModulo]);

            header("Location: $urlModulo&sucesso=publicacao");
            exit;
        }

        if ($acao === "excluir" && $idAulaPost) {
            $stmt = $pdo->prepare("
                DELETE FROM aula
                WHERE id_aula = ?
                  AND id_modulo = ?
            ");
            $stmt->execute([$idAulaPost, $idModulo]);

            header("Location: $urlModulo&sucesso=excluida");
            exit;
        }
    }

    if ($acao === "concluir") {
        if (!$idAulaPost) {
            $erro = "Aula inválida.";
        } else {
            $stmt = $pdo->prepare("
                SELECT id_aula
                FROM aula
                WHERE id_aula = ?
                  AND id_modulo = ?
                  AND ativo = 1
                  AND publicada = 1
                LIMIT 1
            ");
            $stmt->execute([$idAulaPost, $idModulo]);

            if (!$stmt->fetch()) {
                $erro = "Esta aula não está disponível.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO progresso (
                        id_usuario, id_aula,
                        concluida, data_conclusao
                    ) VALUES (?, ?, 1, NOW())
                    ON DUPLICATE KEY UPDATE
                        concluida = 1,
                        data_conclusao = NOW()
                ");
                $stmt->execute([$idUsuario, $idAulaPost]);

                header(
                    "Location: $urlModulo&aula=$idAulaPost&sucesso=concluida"
                );
                exit;
            }
        }
    }
}

/* Buscar aulas */
$sql = "
    SELECT a.id_aula, a.titulo, a.descricao,
           a.conteudo, a.video, a.imagem,
           a.ordem, a.publicada,
           COALESCE(p.concluida, 0) AS concluida
    FROM aula a
    LEFT JOIN progresso p
        ON p.id_aula = a.id_aula
       AND p.id_usuario = ?
    WHERE a.id_modulo = ?
      AND a.ativo = 1
";

if (!$ehAdmin) {
    $sql .= " AND a.publicada = 1";
}

$sql .= " ORDER BY a.ordem ASC, a.id_aula ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idUsuario, $idModulo]);
$aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Progresso apenas das aulas publicadas */
$totalAulas = 0;
$totalConcluidas = 0;

foreach ($aulas as $aula) {
    if ((int)$aula["publicada"] === 1) {
        $totalAulas++;

        if ((int)$aula["concluida"] === 1) {
            $totalConcluidas++;
        }
    }
}

$porcentagem = $totalAulas > 0
    ? round(($totalConcluidas / $totalAulas) * 100)
    : 0;

/* Aula selecionada */
$idAula = filter_input(INPUT_GET, "aula", FILTER_VALIDATE_INT);
$aulaAtual = null;
$indiceAtual = 0;

foreach ($aulas as $indice => $aula) {
    if ($idAula && (int)$aula["id_aula"] === $idAula) {
        $aulaAtual = $aula;
        $indiceAtual = $indice;
        break;
    }
}

if (!$aulaAtual && !empty($aulas)) {
    $aulaAtual = $aulas[0];
    $indiceAtual = 0;
}

$proximaAula = $aulas[$indiceAtual + 1] ?? null;

/* Preparar formulário de edição */
$editar = filter_input(INPUT_GET, "editar", FILTER_VALIDATE_INT);
$aulaEditar = null;

if ($ehAdmin && $editar) {
    foreach ($aulas as $aula) {
        if ((int)$aula["id_aula"] === $editar) {
            $aulaEditar = $aula;
            break;
        }
    }
}

$mostrarFormulario = $ehAdmin &&
    (isset($_GET["nova"]) || $aulaEditar !== null || $erro !== "");

if (isset($_GET["sucesso"])) {
    $mensagens = [
        "salva" => "Aula salva com sucesso!",
        "publicacao" => "Publicação atualizada!",
        "excluida" => "Aula excluída com sucesso!",
        "concluida" => "Aula concluída com sucesso!"
    ];

    $mensagem = $mensagens[$_GET["sucesso"]] ?? "";
}

/* Validar links de vídeo do YouTube */
function obterVideoId($url) {
    if (!$url) return null;

    $partes = parse_url($url);
    if (!$partes || !isset($partes["host"])) return null;

    $host = strtolower($partes["host"]);
    $path = $partes["path"] ?? "";
    $id = null;

    if (in_array($host, [
        "youtube.com", "www.youtube.com",
        "m.youtube.com", "youtube-nocookie.com",
        "www.youtube-nocookie.com"
    ], true)) {
        if ($path === "/watch") {
            parse_str($partes["query"] ?? "", $params);
            $id = $params["v"] ?? null;
        } elseif (preg_match(
            '~^/(embed|shorts)/([A-Za-z0-9_-]{11})/?$~',
            $path, $matches
        )) {
            $id = $matches[2];
        }
    } elseif ($host === "youtu.be") {
        $id = trim($path, "/");
    }

    return preg_match('/^[A-Za-z0-9_-]{11}$/', (string)$id)
        ? $id : null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($modulo["titulo"]) ?> - Sinalize Amor</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}
body {
    background: #F7F9FC;
    color: #555;
}
header {
    background: #7EC8E3;
    color: white;
    padding: 22px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}
header h1 {
    font-size: 27px;
}
.voltar {
    color: white;
    border: 1px solid white;
    border-radius: 25px;
    padding: 10px 20px;
    text-decoration: none;
}
.voltar:hover {
    background: white;
    color: #7EC8E3;
}
.container {
    display: flex;
    min-height: calc(100vh - 85px);
}
.menuAulas {
    width: 330px;
    flex-shrink: 0;
    background: white;
    padding: 30px;
    box-shadow: 3px 0 12px rgba(0,0,0,.08);
}
.menuAulas h2 {
    color: #6F5B47;
    margin-bottom: 20px;
}
.progressoTexto {
    font-size: 13px;
    color: #777;
    margin-bottom: 10px;
}
.barraProgresso {
    height: 10px;
    background: #E8EDF0;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 30px;
}
.progressoPreenchido {
    height: 100%;
    background: #7EC8E3;
    border-radius: 20px;
}
.aula {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #555;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 12px;
    background: #F8F8F8;
    font-size: 14px;
}
.aula:hover {
    background: #EAF7FC;
}
.aula.ativa {
    background: #7EC8E3;
    color: white;
    font-weight: 600;
}
.aulaConcluida {
    color: #419C78;
    font-weight: 700;
}
.aula.ativa .aulaConcluida {
    color: white;
}
.conteudo {
    flex: 1;
    min-width: 0;
    padding: 50px;
}
.conteudoInterno {
    max-width: 950px;
    margin: auto;
}
.etiqueta {
    display: inline-block;
    background: #EAF7FC;
    color: #4398B9;
    padding: 8px 18px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 20px;
}
.conteudo h1 {
    color: #6F5B47;
    font-size: 32px;
    margin-bottom: 20px;
}
.cardConteudo {
    background: white;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,.05);
    margin-top: 25px;
}
.cardConteudo p {
    line-height: 1.8;
    font-size: 16px;
    margin-bottom: 20px;
    overflow-wrap: anywhere;
}
.videoAula {
    width: 100%;
    aspect-ratio: 16 / 9;
    border: none;
    border-radius: 15px;
    margin: 25px 0;
}
.imagemAula {
    max-width: 100%;
    max-height: 450px;
    display: block;
    margin: 25px auto;
    border-radius: 15px;
}
.botao {
    display: inline-block;
    margin-top: 15px;
    padding: 12px 25px;
    background: #7EC8E3;
    color: white;
    border: none;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
}
.botao:hover {
    background: #5FB4D3;
}
.botaoMarrom {
    background: #A68A64;
}
.botaoMarrom:hover {
    background: #8E7352;
}
.botaoVerde {
    background: #78BDA0;
}
.botaoVermelho {
    background: #D97676;
}
.botaoVermelho:hover {
    background: #BD5959;
}
.acoes {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 20px;
}
.mensagem {
    padding: 15px 20px;
    background: #E7F7EF;
    color: #367F59;
    border-radius: 12px;
    margin-bottom: 25px;
}
.erro {
    padding: 15px 20px;
    background: #FCE8E8;
    color: #9B3E3E;
    border-radius: 12px;
    margin-bottom: 25px;
}
.aviso {
    background: #FFF5E8;
    color: #8B6D40;
    padding: 20px;
    border-radius: 12px;
}
.rascunho {
    display: inline-block;
    color: #94641B;
    background: #FFF1D8;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    margin-top: 8px;
}
.formularioAdmin {
    background: white;
    padding: 30px;
    border-radius: 20px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,.06);
}
.formularioAdmin h2 {
    color: #6F5B47;
    margin-bottom: 20px;
}
.formularioAdmin label {
    display: block;
    margin-top: 15px;
    margin-bottom: 6px;
    font-weight: 600;
}
.formularioAdmin input[type="text"],
.formularioAdmin input[type="url"],
.formularioAdmin input[type="number"],
.formularioAdmin textarea {
    width: 100%;
    border: 1px solid #DDD;
    padding: 12px;
    border-radius: 10px;
    font-size: 15px;
}
.formularioAdmin textarea {
    min-height: 110px;
    resize: vertical;
}
.formularioAdmin .checkboxLinha {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 15px;
}
.formularioAdmin .checkboxLinha label {
    margin: 0;
}
@media(max-width:850px) {
    .container {
        flex-direction: column;
    }
    .menuAulas {
        width: 100%;
    }
    .conteudo {
        padding: 30px 20px;
    }
    header {
        padding: 20px;
    }
    header h1 {
        font-size: 22px;
    }
}
</style>
</head>
<body>

<header>
    <h1>🤟 <?= e($modulo["titulo"]) ?></h1>
    <a href="cursos.php" class="voltar">
        ← Voltar aos cursos
    </a>
</header>

<div class="container">

    <aside class="menuAulas">
        <h2>Aulas</h2>

        <div class="progressoTexto">
            <?= $totalConcluidas ?> de <?= $totalAulas ?>
            aulas concluídas (<?= $porcentagem ?>%)
        </div>

        <div class="barraProgresso"
             role="progressbar"
             aria-label="Progresso do módulo"
             aria-valuenow="<?= $porcentagem ?>"
             aria-valuemin="0"
             aria-valuemax="100">
            <div class="progressoPreenchido"
                 style="width: <?= $porcentagem ?>%;"></div>
        </div>

        <?php if ($ehAdmin): ?>
            <a href="<?= e($urlModulo) ?>&nova=1"
               class="botao"
               style="display:block;text-align:center;margin-bottom:25px;">
                + Criar aula
            </a>
        <?php endif; ?>

        <?php foreach ($aulas as $indice => $aula): ?>
            <?php
            $ativa = $aulaAtual &&
                (int)$aulaAtual["id_aula"] === (int)$aula["id_aula"];
            ?>

            <a href="<?= e($urlModulo) ?>&aula=<?= (int)$aula["id_aula"] ?>"
               class="aula <?= $ativa ? 'ativa' : '' ?>">
                <span>
                    📖 Aula <?= $indice + 1 ?> -
                    <?= e($aula["titulo"]) ?>

                    <?php if ($ehAdmin && !(int)$aula["publicada"]): ?>
                        <span class="rascunho">Rascunho</span>
                    <?php endif; ?>
                </span>

                <?php if ((int)$aula["concluida"]): ?>
                    <span class="aulaConcluida">✓</span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>

        <?php if (!$aulas): ?>
            <p>Nenhuma aula disponível.</p>
        <?php endif; ?>
    </aside>

    <main class="conteudo">
        <div class="conteudoInterno">

            <?php if ($mensagem): ?>
                <div class="mensagem"><?= e($mensagem) ?></div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="erro"><?= e($erro) ?></div>
            <?php endif; ?>

            <?php if ($mostrarFormulario): ?>
                <section class="formularioAdmin">
                    <h2>
                        <?= $aulaEditar ? "Editar aula" : "Criar nova aula" ?>
                    </h2>

                    <form method="POST" action="<?= e($urlModulo) ?>">
                        <input type="hidden" name="csrf_token"
                               value="<?= e($_SESSION["csrf_token"]) ?>">
                        <input type="hidden" name="acao" value="salvar">
                        <input type="hidden" name="id_aula"
                               value="<?= (int)($aulaEditar["id_aula"] ?? 0) ?>">

                        <label for="titulo">Título da aula</label>
                        <input id="titulo" type="text" name="titulo"
                               maxlength="150" required
                               value="<?= e($aulaEditar["titulo"] ?? "") ?>">

                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao"><?= e($aulaEditar["descricao"] ?? "") ?></textarea>

                        <label for="conteudo">Conteúdo da aula</label>
                        <textarea id="conteudo" name="conteudo"
                                  style="min-height:220px;"><?= e($aulaEditar["conteudo"] ?? "") ?></textarea>

                        <label for="video">Link do vídeo (YouTube)</label>
                        <input id="video" type="url" name="video"
                               placeholder="https://www.youtube.com/watch?v=..."
                               value="<?= e($aulaEditar["video"] ?? "") ?>">

                        <label for="ordem">Ordem da aula</label>
                        <input id="ordem" type="number" name="ordem"
                               min="1" required
                               value="<?= (int)($aulaEditar["ordem"] ?? 1) ?>">

                        <div class="checkboxLinha">
                            <input type="checkbox" id="publicada"
                                   name="publicada" value="1"
                                   <?= !empty($aulaEditar["publicada"]) ? "checked" : "" ?>>
                            <label for="publicada">
                                Publicar aula para os alunos
                            </label>
                        </div>

                        <div class="acoes">
                            <button type="submit" class="botao">
                                Salvar aula
                            </button>
                            <a href="<?= e($urlModulo) ?>"
                               class="botao botaoMarrom">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($aulaAtual): ?>

                <span class="etiqueta">
                    Aula <?= $indiceAtual + 1 ?>
                </span>

                <h1><?= e($aulaAtual["titulo"]) ?></h1>

                <?php if ($ehAdmin && !(int)$aulaAtual["publicada"]): ?>
                    <span class="rascunho">Aula não publicada</span>
                <?php endif; ?>

                <div class="cardConteudo">

                    <?php if ($aulaAtual["descricao"]): ?>
                        <p><?= nl2br(e($aulaAtual["descricao"])) ?></p>
                    <?php endif; ?>

                    <?php if ($aulaAtual["conteudo"]): ?>
                        <p><?= nl2br(e($aulaAtual["conteudo"])) ?></p>
                    <?php else: ?>
                        <p>O conteúdo desta aula está em desenvolvimento.</p>
                    <?php endif; ?>

                    <?php
                    $videoId = obterVideoId($aulaAtual["video"]);
                    ?>

                    <?php if ($videoId): ?>
                        <iframe class="videoAula"
                                src="https://www.youtube-nocookie.com/embed/<?= e($videoId) ?>"
                                title="Vídeo da aula"
                                loading="lazy"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen></iframe>
                    <?php endif; ?>

                    <?php
                    $imagem = basename((string)($aulaAtual["imagem"] ?? ""));
                    if ($imagem && preg_match(
                        '/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$/i',
                        $imagem
                    )):
                    ?>
                        <img class="imagemAula"
                             src="../uploads/aulas/<?= rawurlencode($imagem) ?>"
                             alt="Imagem ilustrativa da aula">
                    <?php endif; ?>

                    <div class="acoes">
                        <?php if ((int)$aulaAtual["publicada"] === 1): ?>

                            <?php if ((int)$aulaAtual["concluida"] === 1): ?>
                                <button class="botao botaoVerde" disabled>
                                    ✓ Aula concluída
                                </button>
                            <?php else: ?>
                                <form method="POST"
                                      action="<?= e($urlModulo) ?>">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= e($_SESSION["csrf_token"]) ?>">
                                    <input type="hidden" name="acao"
                                           value="concluir">
                                    <input type="hidden" name="id_aula"
                                           value="<?= (int)$aulaAtual["id_aula"] ?>">
                                    <button class="botao" type="submit">
                                        ✓ Marcar como concluída
                                    </button>
                                </form>
                            <?php endif; ?>

                        <?php endif; ?>

                        <?php if ($proximaAula): ?>
                            <a class="botao botaoMarrom"
                               href="<?= e($urlModulo) ?>&aula=<?= (int)$proximaAula["id_aula"] ?>">
                                Próxima aula →
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if ($ehAdmin): ?>
                        <div class="acoes"
                             style="border-top:1px solid #EEE;padding-top:20px;">

                            <a href="<?= e($urlModulo) ?>&editar=<?= (int)$aulaAtual["id_aula"] ?>&aula=<?= (int)$aulaAtual["id_aula"] ?>"
                               class="botao botaoMarrom">
                                ✏️ Editar aula
                            </a>

                            <form method="POST"
                                  action="<?= e($urlModulo) ?>">
                                <input type="hidden" name="csrf_token"
                                       value="<?= e($_SESSION["csrf_token"]) ?>">
                                <input type="hidden" name="acao"
                                       value="publicar">
                                <input type="hidden" name="id_aula"
                                       value="<?= (int)$aulaAtual["id_aula"] ?>">
                                <button type="submit" class="botao">
                                    <?= (int)$aulaAtual["publicada"]
                                        ? "Despublicar"
                                        : "Publicar aula" ?>
                                </button>
                            </form>

                            <form method="POST"
                                  action="<?= e($urlModulo) ?>"
                                  onsubmit="return confirm('Excluir esta aula permanentemente?');">
                                <input type="hidden" name="csrf_token"
                                       value="<?= e($_SESSION["csrf_token"]) ?>">
                                <input type="hidden" name="acao"
                                       value="excluir">
                                <input type="hidden" name="id_aula"
                                       value="<?= (int)$aulaAtual["id_aula"] ?>">
                                <button type="submit"
                                        class="botao botaoVermelho">
                                    🗑️ Excluir aula
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>

            <?php else: ?>
                <h1>Nenhuma aula disponível</h1>
                <div class="aviso">
                    Este módulo ainda não possui aulas publicadas.
                    <?php if ($ehAdmin): ?>
                        Clique em "Criar aula" para adicionar a primeira.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>
</div>

</body>
</html>
