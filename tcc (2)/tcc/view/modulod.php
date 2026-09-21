<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Módulo 2</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#F7F9FC;
}

header{
    background:#7EC8E3;
    color:white;
    padding:25px 40px;
    font-size:28px;
    font-weight:600;
}

.container{
    display:flex;
    min-height:calc(100vh - 88px);
}

.menuAulas{
    width:330px;
    background:white;
    box-shadow:3px 0 12px rgba(0,0,0,.08);
    padding:30px;
}

.menuAulas h2{
    color:#6F5B47;
    margin-bottom:25px;
}

.aula{
    display:block;
    text-decoration:none;
    color:#555;
    padding:15px 18px;
    border-radius:12px;
    margin-bottom:12px;
    transition:.3s;
    background:#F8F8F8;
}

.aula:hover{
    background:#7EC8E3;
    color:white;
}

.aula.ativa{
    background:#7EC8E3;
    color:white;
    font-weight:600;
}

.conteudo{
    flex:1;
    padding:50px;
}

.conteudo h1{
    color:#6F5B47;
    margin-bottom:20px;
}

.conteudo p{
    color:#666;
    line-height:1.8;
    font-size:17px;
}

.botao{
    display:inline-block;
    margin-top:35px;
    padding:14px 35px;
    background:#7EC8E3;
    color:white;
    text-decoration:none;
    border-radius:30px;
    font-weight:600;
}

.botao:hover{
    background:#5FB4D3;
}

</style>
</head>

<body>

<header>
Módulo 2
</header>

<div class="container">

<div class="menuAulas">

<h2>Aulas</h2>

<a class="aula ativa" href="#">
📖 Aula 1 - Alfabeto Manual
</a>

<a class="aula" href="#">
📖 Aula 2 - Números
</a>

<a class="aula" href="#">
📖 Aula 3 - Saudações
</a>

<a class="aula" href="#">
📖 Aula 4 - Apresentações
</a>

<a class="aula" href="#">
📖 Aula 5 - Pronomes
</a>

<a class="aula" href="#">
📖 Aula 6 - Pessoas
</a>

<a class="aula" href="#">
📖 Aula 7 - Cores
</a>

<a class="aula" href="#">
📖 Aula 8 - Tempo
</a>

<a class="aula" href="#">
📖 Aula 9 - Calendário
</a>

<a class="aula" href="#">
📖 Aula 10 - Perguntas
</a>

</div>

<div class="conteudo">

<h1>Aula 1 - Alfabeto Manual</h1>

<p>
Aqui ficará o conteúdo da aula. Posteriormente você poderá adicionar vídeos,
imagens, textos explicativos e exercícios relacionados ao tema.
</p>

<a href="#" class="botao">
Marcar como concluída
</a>

</div>

</div>

</body>
</html>