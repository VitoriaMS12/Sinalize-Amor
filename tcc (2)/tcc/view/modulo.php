<?php

session_start();

require_once "../model/conexao.php";


// Verifica se foi informado um módulo
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Módulo não informado.");
}

$idModulo = (int) $_GET["id"];


// Busca o módulo
$sqlModulo = "
    SELECT *
    FROM modulo
    WHERE id_modulo = :id_modulo
";

$stmtModulo = $pdo->prepare($sqlModulo);
$stmtModulo->execute([
    ":id_modulo" => $idModulo
]);

$modulo = $stmtModulo->fetch(PDO::FETCH_ASSOC);


// Se o módulo não existir
if (!$modulo) {
    die("Módulo não encontrado.");
}


// Busca as aulas desse módulo
$sqlAulas = "
    SELECT *
    FROM aula
    WHERE id_modulo = :id_modulo
    AND ativo = TRUE
    ORDER BY ordem
";

$stmtAulas = $pdo->prepare($sqlAulas);
$stmtAulas->execute([
    ":id_modulo" => $idModulo
]);

$aulas = $stmtAulas->fetchAll(PDO::FETCH_ASSOC);


// pega as aulas concluidas pelo usuario.
$progressoAulas = [];

if (isset($_SESSION["id_usuario"])) {

    $sqlProgresso = "
        SELECT id_aula
        FROM progresso
        WHERE id_usuario = :id_usuario
        AND concluida = TRUE
    ";

    $stmtProgresso = $pdo->prepare($sqlProgresso);

    $stmtProgresso->execute([
        ":id_usuario" => $_SESSION["id_usuario"]
    ]);

    $progressoAulas = $stmtProgresso->fetchAll(PDO::FETCH_COLUMN);
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($modulo["titulo"]); ?>
    </title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/moduloaStyle.css">
</head>


<body>


<header>

    <?php echo htmlspecialchars($modulo["titulo"]); ?>

</header>


<div class="container">


    <div class="menuAulas">

        <h2>Aulas</h2>


        <?php foreach ($aulas as $index => $aula): ?>

            <a 
                class="aula <?php echo $index === 0 ? 'ativa' : ''; ?>"
                href="?id=<?php echo $idModulo; ?>&aula=<?php echo $aula["id_aula"]; ?>"
            >

                📖 Aula <?php echo $aula["ordem"]; ?> -
                <?php echo htmlspecialchars($aula["titulo"]); ?>

            </a>

        <?php endforeach; ?>


    </div>



    <div class="conteudo">


        <?php if (count($aulas) > 0): ?>

            <?php

            // Verifica se o usuário escolheu uma aula
            if (isset($_GET["aula"]) && is_numeric($_GET["aula"])) {

                $idAula = (int) $_GET["aula"];

                $aulaAtual = null;

                foreach ($aulas as $aula) {

                    if ($aula["id_aula"] == $idAula) {

                        $aulaAtual = $aula;

                        break;

                    }

                }

            } else {

                $aulaAtual = $aulas[0];

            }

            ?>


            <h1>

                Aula <?php echo $aulaAtual["ordem"]; ?> -
                <?php echo htmlspecialchars($aulaAtual["titulo"]); ?>

            </h1>


            <p>

                <?php echo htmlspecialchars($aulaAtual["descricao"]); ?>

            </p>


            <p>

                Aqui ficará o conteúdo da aula. Posteriormente você poderá adicionar vídeos,
                imagens, textos explicativos e exercícios relacionados ao tema.

            </p>


            <?php if (in_array($aulaAtual["id_aula"], $progressoAulas)): ?>

    <button type="button" class="botao" disabled>

        ✅ Aula concluída

    </button>

<?php else: ?>

    <form action="../control/marcarAula.php" method="POST">

        <input 
            type="hidden" 
            name="id_aula" 
            value="<?php echo $aulaAtual["id_aula"]; ?>"
        >

        <input 
            type="hidden" 
            name="id_modulo" 
            value="<?php echo $idModulo; ?>"
        >

        <button type="submit" class="botao">

            Marcar como concluída

        </button>

    </form>

<?php endif; ?>


        <?php else: ?>

            <h1>Este módulo ainda não possui aulas.</h1>

        <?php endif; ?>


    </div>


</div>


</body>

</html>