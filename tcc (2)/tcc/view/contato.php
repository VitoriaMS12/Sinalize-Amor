<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contato | Sinalize Amor</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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

        .botaoVoltar {
            background: #7EC8E3;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: .3s;
        }

        .botaoVoltar:hover {
            background: #68B8D8;
        }

        .sloganSite {
            text-align: center;
            padding-bottom: 15px;
            color: #A68A64;
            font-style: italic;
        }

        .bannerContato {
            background: linear-gradient(135deg, #EAF7FC, #F7F1E8);
            text-align: center;
            padding: 85px 20px;
        }

        .bannerContato h1 {
            font-size: 52px;
            color: #6F5B47;
            margin-bottom: 20px;
        }

        .bannerContato p {
            max-width: 750px;
            margin: auto;
            font-size: 19px;
            line-height: 1.8;
            color: #777;
        }

        .containerContato {
            width: 90%;
            max-width: 1150px;
            margin: auto;
        }

        .secaoContato {
            padding: 75px 0;
        }

        .tituloSecao {
            text-align: center;
            color: #6F5B47;
            font-size: 36px;
            margin-bottom: 15px;
        }

        .subtituloSecao {
            text-align: center;
            color: #777;
            line-height: 1.8;
            max-width: 750px;
            margin: 0 auto 50px;
        }


        .areaSuporte {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            align-items: stretch;
        }

        .cardSuporte {
            background: white;
            border-radius: 20px;
            padding: 40px;

            box-shadow: 0 10px 30px rgba(0,0,0,.08);

            text-align: center;
        }

        .iconeSuporte {
            width: 75px;
            height: 75px;

            margin: 0 auto 20px;

            background: #EAF7FC;
            color: #7EC8E3;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 32px;
        }

        .cardSuporte h2 {
            color: #6F5B47;
            margin-bottom: 15px;
        }

        .cardSuporte p {
            color: #777;
            line-height: 1.8;
            margin-bottom: 25px;
        }


        .qrCode {
            width: 190px;
            height: 190px;

            object-fit: cover;

            border-radius: 12px;

            margin: 10px auto 20px;

            border: 5px solid #F7F1E8;
        }

        .botaoWhatsapp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;

            background: #7EC8E3;
            color: white;

            padding: 13px 25px;

            border-radius: 30px;

            text-decoration: none;
            font-weight: 600;

            transition: .3s;
        }

        .botaoWhatsapp:hover {
            background: #68B8D8;
            transform: translateY(-3px);
        }


        .contatosLista {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 25px;
        }

        .itemContato {
            display: flex;
            align-items: center;
            gap: 15px;

            padding: 17px;

            background: #FDFBF8;

            border-radius: 14px;

            text-decoration: none;

            transition: .3s;
        }

        .itemContato:hover {
            transform: translateX(5px);
            background: #EAF7FC;
        }

        .iconeContato {
            width: 48px;
            height: 48px;

            flex-shrink: 0;

            background: #EAF7FC;
            color: #7EC8E3;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 21px;
        }

        .textoContato {
            text-align: left;
        }

        .textoContato strong {
            display: block;
            color: #6F5B47;
            margin-bottom: 3px;
        }

        .textoContato span {
            color: #777;
            font-size: 14px;
        }


        .secaoRecepcao {
            background: #EAF7FC;
            padding: 75px 0;
        }

        .cardRecepcao {
            max-width: 850px;
            margin: auto;

            background: white;

            padding: 40px;

            border-radius: 20px;

            text-align: center;

            box-shadow: 0 10px 30px rgba(0,0,0,.07);
        }

        .iconeRecepcao {
            font-size: 45px;
            color: #7EC8E3;
            margin-bottom: 15px;
        }

        .cardRecepcao h2 {
            color: #6F5B47;
            margin-bottom: 15px;
        }

        .cardRecepcao p {
            color: #777;
            line-height: 1.9;
        }


        .horario {
            margin-top: 25px;

            display: inline-flex;
            align-items: center;
            gap: 10px;

            color: #6F5B47;
            font-weight: 600;
        }

        .horario i {
            color: #7EC8E3;
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

        .rodapeSite h2 span {
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

            .bannerContato h1 {
                font-size: 40px;
            }

            .areaSuporte {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 600px) {

            .logoSite {
                font-size: 28px;
            }

            .bannerContato h1 {
                font-size: 32px;
            }

            .bannerContato p {
                font-size: 17px;
            }

            .tituloSecao {
                font-size: 30px;
            }

            .cardSuporte,
            .cardRecepcao {
                padding: 28px 20px;
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
                <a href="inicio.php">
                    Início
                </a>
            </li>

            <li>
                <a href="sobre.php">
                    Sobre
                </a>
            </li>

            <li>
                <a href="cursos.php">
                    Cursos
                </a>
            </li>

            <li>
                <a href="comunidade.php">
                    Comunidade
                </a>
            </li>

        </ul>

        <a href="inicio.php" class="botaoVoltar">
            Voltar
        </a>

    </div>

    <div class="sloganSite">
        A Caminho da Inclusão
    </div>

</header>


<section class="bannerContato">

    <h1>
        Entre em contato
    </h1>

    <p>
        Ficou com alguma dúvida? Precisa de ajuda ou quer saber
        mais sobre o Sinalize Amor? Entre em contato com nossa
        equipe e acesse nosso suporte.
    </p>

</section>


<section class="secaoContato">

    <div class="containerContato">

        <h2 class="tituloSecao">
            Estamos aqui para ajudar!
        </h2>

        <p class="subtituloSecao">

            Utilize um dos nossos canais de atendimento para
            entrar em contato conosco. Você pode falar diretamente
            pelo WhatsApp ou acompanhar nossas redes sociais.

        </p>


        <div class="areaSuporte">



            <div class="cardSuporte">

                <div class="iconeSuporte">

                    <i class="fa-brands fa-whatsapp"></i>

                </div>

                <h2>
                    Fale conosco pelo WhatsApp
                </h2>

                <p>

                    Aponte a câmera do seu celular para o QR Code
                    abaixo ou clique no botão para iniciar uma
                    conversa diretamente pelo WhatsApp.

                </p>


                <img
                    src="../style/qrcode-whatsapp.png"
                    alt="QR Code do WhatsApp"
                    class="qrCode"
                >

                <br>

                <a
                    href="https://wa.me/5511990060332"
                    target="_blank"
                    class="botaoWhatsapp"
                >

                    <i class="fa-brands fa-whatsapp"></i>

                    Abrir WhatsApp

                </a>

            </div>



            <div class="cardSuporte">

                <div class="iconeSuporte">

                    <i class="fa-solid fa-comments"></i>

                </div>

                <h2>
                    Nossas redes e contatos
                </h2>

                <p>

                    Acompanhe o Sinalize Amor e fique por dentro
                    das novidades, conteúdos e atualizações
                    do projeto.

                </p>


                <div class="contatosLista">


                    <a
                        href="#"
                        target="_blank"
                        class="itemContato"
                    >

                        <div class="iconeContato">

                            <i class="fa-brands fa-instagram"></i>

                        </div>

                        <div class="textoContato">

                            <strong>
                                Instagram
                            </strong>

                            <span>
                                @seuinstagram
                            </span>

                        </div>

                    </a>



                    <a
                        href="#"
                        target="_blank"
                        class="itemContato"
                    >

                        <div class="iconeContato">

                            <i class="fa-brands fa-facebook-f"></i>

                        </div>

                        <div class="textoContato">

                            <strong>
                                Facebook
                            </strong>

                            <span>
                                Sinalize Amor
                            </span>

                        </div>

                    </a>


                    <!-- EMAIL -->

                    <a
                        href="mailto:seuemail@email.com"
                        class="itemContato"
                    >

                        <div class="iconeContato">

                            <i class="fa-solid fa-envelope"></i>

                        </div>

                        <div class="textoContato">

                            <strong>
                                E-mail
                            </strong>

                            <span>
                                seuemail@email.com
                            </span>

                        </div>

                    </a>


                    <!-- TELEFONE -->

                    <a
                        href="tel:+5500000000000"
                        class="itemContato"
                    >

                        <div class="iconeContato">

                            <i class="fa-solid fa-phone"></i>

                        </div>

                        <div class="textoContato">

                            <strong>
                                Telefone
                            </strong>

                            <span>
                                (00) 00000-0000
                            </span>

                        </div>

                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     RECEPÇÃO / SUPORTE
========================= -->

<section class="secaoRecepcao">

    <div class="containerContato">

        <div class="cardRecepcao">

            <div class="iconeRecepcao">

                <i class="fa-solid fa-headset"></i>

            </div>

            <h2>
                Precisa de atendimento?
            </h2>

            <p>

                Nossa equipe está disponível para auxiliar
                você com dúvidas relacionadas à plataforma,
                aos cursos e ao funcionamento do Sinalize Amor.

                <br><br>

                Entre em contato por um dos nossos canais
                de atendimento e teremos prazer em ajudar.

            </p>


            <div class="horario">

                <i class="fa-regular fa-clock"></i>

                Atendimento:
                <strong> [HORÁRIO DA RECEPÇÃO] </strong>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     RODAPÉ
========================= -->

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