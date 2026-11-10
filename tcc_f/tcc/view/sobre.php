<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sobre | Sinalize Amor</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: #FDFBF8;
            color: #555;
        }


        .headerSite {
            background: #EAF7FC;
            box-shadow: 0 3px 15px rgba(0,0,0,.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbarPrincipal {
            width: 90%;
            max-width: 1300px;
            margin: auto;
            height: 85px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logoSite {
            font-size: 34px;
            font-weight: 700;
            color: #6F5B47;
        }

        .logoSite span {
            color: #7EC8E3;
        }

        .menuPrincipal {
            list-style: none;
            display: flex;
            gap: 35px;
        }

        .menuPrincipal a {
            text-decoration: none;
            color: #6F5B47;
            font-weight: 500;
            transition: .3s;
        }

        .menuPrincipal a:hover {
            color: #7EC8E3;
        }

        .botaoContato {
            background: #7EC8E3;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: .3s;
        }

        .botaoContato:hover {
            background: #68B8D8;
        }

        .sloganSite {
            text-align: center;
            padding-bottom: 15px;
            color: #A68A64;
            font-style: italic;
        }


        .bannerSobre {
            background: linear-gradient(135deg, #EAF7FC, #F7F1E8);

            text-align: center;
            padding: 85px 20px;
        }

        .bannerSobre h1 {
            font-size: 52px;
            color: #6F5B47;
            margin-bottom: 20px;
        }

        .bannerSobre p {
            font-size: 20px;
            color: #777;
            max-width: 750px;
            margin: auto;
            line-height: 1.8;
        }


        .containerSobre {
            width: 90%;
            max-width: 1150px;
            margin: auto;
        }

        .secaoSobre {
            padding: 75px 0;
        }

        .secaoSobre:nth-child(even) {
            background: #EAF7FC;
        }

        .tituloSecao {
            color: #6F5B47;
            font-size: 36px;
            margin-bottom: 25px;
            text-align: center;
        }

        .textoSecao {
            max-width: 950px;
            margin: auto;
            text-align: center;
            color: #666;
            font-size: 17px;
            line-height: 2;
        }


        .cardsObjetivos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 45px;
        }

        .cardObjetivo {
            background: white;
            padding: 35px;
            border-radius: 18px;
            text-align: center;

            box-shadow: 0 10px 25px rgba(0,0,0,.07);

            transition: .3s;
        }

        .cardObjetivo:hover {
            transform: translateY(-8px);
        }

        .iconeObjetivo {
            font-size: 45px;
            margin-bottom: 20px;
        }

        .cardObjetivo h3 {
            color: #6F5B47;
            margin-bottom: 15px;
            font-size: 21px;
        }

        .cardObjetivo p {
            color: #777;
            line-height: 1.8;
        }


        .origemProjeto {
            display: flex;
            align-items: center;
            gap: 60px;
        }

        .origemTexto {
            flex: 1;
        }

        .origemTexto h2 {
            color: #6F5B47;
            font-size: 36px;
            margin-bottom: 25px;
        }

        .origemTexto p {
            color: #666;
            line-height: 2;
            font-size: 17px;
        }

        .origemImagem {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .origemImagem img {
            width: 100%;
            max-width: 430px;
            border-radius: 25px;
        }


        .integrantes {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 45px;
        }

        .cardIntegrante {
            background: white;
            padding: 35px 20px;
            border-radius: 18px;
            text-align: center;

            box-shadow: 0 10px 25px rgba(0,0,0,.07);
        }

        .avatarIntegrante {
            width: 85px;
            height: 85px;

            background: #EAF7FC;
            border: 3px solid #7EC8E3;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 38px;
            margin: 0 auto 20px;
        }

        .cardIntegrante h3 {
            color: #6F5B47;
            font-size: 21px;
            margin-bottom: 10px;
        }

        .cardIntegrante p {
            color: #777;
            line-height: 1.7;
        }


        .informacoesTcc {
            background: #F7F1E8;
            padding: 45px;
            border-radius: 20px;
            max-width: 900px;
            margin: 40px auto 0;

            box-shadow: 0 8px 25px rgba(0,0,0,.06);
        }

        .informacoesTcc p {
            color: #666;
            line-height: 2;
            margin-bottom: 10px;
        }

        .informacoesTcc strong {
            color: #6F5B47;
        }


        .rodapeSite {
            background: #F7F1E8;
            text-align: center;
            padding: 50px 20px;
        }

        .rodapeSite h2 {
            color: #6F5B47;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .rodapeSite span {
            color: #7EC8E3;
        }

        .rodapeSite p {
            color: #777;
            line-height: 1.8;
        }

        .copyright {
            margin-top: 25px;
            font-size: 14px;
        }


        @media(max-width: 900px) {

            .navbarPrincipal {
                flex-direction: column;
                height: auto;
                padding: 25px 0;
                gap: 20px;
            }

            .menuPrincipal {
                flex-wrap: wrap;
                justify-content: center;
            }

            .bannerSobre h1 {
                font-size: 40px;
            }

            .cardsObjetivos,
            .integrantes {
                grid-template-columns: 1fr;
            }

            .origemProjeto {
                flex-direction: column;
            }

            .origemTexto {
                text-align: center;
            }

        }

        @media(max-width: 600px) {

            .logoSite {
                font-size: 28px;
            }

            .bannerSobre h1 {
                font-size: 32px;
            }

            .bannerSobre p {
                font-size: 17px;
            }

            .tituloSecao,
            .origemTexto h2 {
                font-size: 30px;
            }

            .informacoesTcc {
                padding: 25px;
            }

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

                <li>
                    <a href="inicio.php">Início</a>
                </li>

                <li>
                    <a href="cursos.php">Cursos</a>
                </li>

                <li>
                    <a href="comunidade.php">Comunidade</a>
                </li>

                <li>
                    <a href="contato.php">Contato</a>
                </li>

            </ul>

            <a href="inicio.php" class="botaoContato">
                Voltar
            </a>

        </div>

        <div class="sloganSite">
            A Caminho da Inclusão
        </div>

    </header>


    <section class="bannerSobre">

        <h1>Sobre o Sinalize Amor</h1>

        <p>
            Um projeto criado para aproximar pessoas por meio da
            Língua Brasileira de Sinais e contribuir para uma
            comunicação mais acessível e inclusiva.
        </p>

    </section>
    <section class="secaoSobre">

        <div class="containerSobre">

            <h2 class="tituloSecao">
                Sobre o nosso trabalho
            </h2>

            <p class="textoSecao">

                O Sinalize Amor é uma plataforma digital desenvolvida
                com o propósito de promover o aprendizado da Língua
                Brasileira de Sinais (Libras) e incentivar a inclusão
                na comunicação.

                <br><br>

                A proposta é oferecer conteúdos teóricos e práticos
                de maneira simples, acolhedora e acessível, permitindo
                que mais pessoas tenham contato com Libras e
                desenvolvam conhecimentos que possam ser utilizados
                em diferentes situações do cotidiano.

            </p>

        </div>

    </section>


    <!-- OBJETIVO -->

    <section class="secaoSobre">

        <div class="containerSobre">

            <h2 class="tituloSecao">
                Qual é o nosso objetivo?
            </h2>

            <p class="textoSecao">

                Nosso principal objetivo é levar acessibilidade em
                Libras e contribuir para uma comunicação maior entre
                estudantes de Libras, intérpretes, pessoas ouvintes
                e a comunidade surda.

                <br><br>

                Por meio da plataforma, buscamos incentivar o
                aprendizado, a troca de conhecimentos e a aproximação
                entre diferentes pessoas, fortalecendo a importância
                da comunicação inclusiva.

            </p>


            <div class="cardsObjetivos">

                <div class="cardObjetivo">

                    <div class="iconeObjetivo">
                        🤟
                    </div>

                    <h3>
                        Acessibilidade em Libras
                    </h3>

                    <p>
                        Incentivar o acesso ao conhecimento de Libras
                        e contribuir para uma comunicação mais
                        acessível.
                    </p>

                </div>


                <div class="cardObjetivo">

                    <div class="iconeObjetivo">
                        📚
                    </div>

                    <h3>
                        Aprendizado
                    </h3>

                    <p>
                        Disponibilizar conteúdos teóricos, vídeos e
                        exercícios para auxiliar estudantes no
                        aprendizado da língua.
                    </p>

                </div>


                <div class="cardObjetivo">

                    <div class="iconeObjetivo">
                        🤝
                    </div>

                    <h3>
                        Conexão e inclusão
                    </h3>

                    <p>
                        Aproximar estudantes, intérpretes e a
                        comunidade surda por meio da comunicação
                        e da troca de experiências.
                    </p>

                </div>

            </div>

        </div>

    </section>


    <section class="secaoSobre">

        <div class="containerSobre">

            <div class="origemProjeto">

                <div class="origemTexto">

                    <h2>
                        Onde surgiu o Sinalize Amor?
                    </h2>

                    <p>

                        O Sinalize Amor surgiu como um projeto de
                        Trabalho de Conclusão de Curso (TCC),
                        desenvolvido por alunas da ETEC da Zona Leste.

                        <br><br>

                        A ideia nasceu da vontade de criar uma
                        plataforma que contribuísse para o aprendizado
                        de Libras e para a construção de uma sociedade
                        mais inclusiva, aproximando pessoas por meio
                        da comunicação.

                    </p>

                </div>


                <div class="origemImagem">


                    <img
                        src="../style/imagem1.jpg"
                        alt="Ilustração sobre comunicação em Libras"
                    >

                </div>

            </div>

        </div>

    </section>



    <section class="secaoSobre">

        <div class="containerSobre">

            <h2 class="tituloSecao">
                Quem desenvolveu o projeto?
            </h2>

            <p class="textoSecao">

                O Sinalize Amor foi desenvolvido como parte da
                conclusão do curso de Desenvolvimento de Sistemas
                da ETEC da Zona Leste.

            </p>


            <div class="integrantes">

                <div class="cardIntegrante">

                    <div class="avatarIntegrante">
                        👩🏻‍💻
                    </div>

                    <h3>
                        Nathaly Pereira
                    </h3>

                    <p>
                        Aluna desenvolvedora do projeto.
                    </p>

                </div>


                <div class="cardIntegrante">

                    <div class="avatarIntegrante">
                        👩🏻‍💻
                    </div>

                    <h3>
                        Vitória Marques
                    </h3>

                    <p>
                        Aluna desenvolvedora do projeto.
                    </p>

                </div>


                <div class="cardIntegrante">

                    <div class="avatarIntegrante">
                        👩🏻‍💻
                    </div>

                    <h3>
                        Maria Eduarda Gomes
                    </h3>

                    <p>
                        Aluna desenvolvedora do projeto.
                    </p>

                </div>

            </div>


            <div class="informacoesTcc">

                <p>
                    <strong>Instituição:</strong>
                    ETEC da Zona Leste
                </p>

                <p>
                    <strong>Curso:</strong>
                    Desenvolvimento de Sistemas
                </p>

                <p>
                    <strong>Trabalho:</strong>
                    Trabalho de Conclusão de Curso (TCC)
                </p>

                <p>
                    <strong>Ano de conclusão:</strong>
                    2026
                </p>

                <p>
                    <strong>Professor coordenador:</strong>
                    Marcelo Collado
                </p>

            </div>

        </div>

    </section>


    <!-- RODAPÉ -->

    <footer class="rodapeSite">

        <h2>
            Sinalize<span>Amor</span>
        </h2>

        <p>
            Tornando a comunicação mais acessível através
            da Língua Brasileira de Sinais.
        </p>

        <p class="copyright">
            © <?php echo date("Y"); ?>
            Sinalize Amor • Todos os direitos reservados.
        </p>

    </footer>

</body>

</html>