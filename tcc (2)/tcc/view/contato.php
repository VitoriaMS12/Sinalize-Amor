<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato | Sinalize Amor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../CSS/contatoStyle.css">
</head>

<body>
<header class="headerSite">

    <div class="navbarPrincipal">

        <div class="logoSite"> Sinalize<span>Amor</span></div>

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
                <a href="comunidade.php"> Comunidade</a>
            </li>
        </ul>

        <a href="inicio.php" class="botaoVoltar"> Voltar </a>

    </div>

    <div class="sloganSite"> A Caminho da Inclusão </div>

</header>


<section class="bannerContato">

    <h1> Entre em contato </h1>

    <p>
        Ficou com alguma dúvida? Precisa de ajuda ou quer saber
        mais sobre o Sinalize Amor? Entre em contato com nossa
        equipe e acesse nosso suporte.
    </p>

</section>


<section class="secaoContato">

    <div class="containerContato">

        <h2 class="tituloSecao"> Estamos aqui para ajudar! </h2>

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

                <h2> Fale conosco pelo WhatsApp </h2>

                <p>
                    Aponte a câmera do seu celular para o QR Code
                    abaixo ou clique no botão para iniciar uma
                    conversa diretamente pelo WhatsApp.
                </p>

                <img src="../style/qrcode-whatsapp.png" alt="QR Code do WhatsApp" class="qrCode">

                <br>

                <a href="https://wa.me/5511990060332" target="_blank" class="botaoWhatsapp">

                    <i class="fa-brands fa-whatsapp"></i>
                    Abrir WhatsApp
                </a>

            </div>


            <div class="cardSuporte">
                <div class="iconeSuporte">
                    <i class="fa-solid fa-comments"></i>
                </div>

                <h2> Nossas redes e contatos </h2>

                <p>
                    Acompanhe o Sinalize Amor e fique por dentro
                    das novidades, conteúdos e atualizações
                    do projeto.
                </p>

                <!--Lista/bloco para entrar em contato -->  
                <div class="contatosLista">

                    <a href="#" target="_blank" class="itemContato">
                        <div class="iconeContato">
                            <i class="fa-brands fa-instagram"></i>
                        </div>

                        <div class="textoContato">
                            <strong> Instagram </strong>
                            <span> @seuinstagram </span>
                        </div>
                    </a>



                    <a href="#" target="_blank" class="itemContato">
                        <div class="iconeContato">
                            <i class="fa-brands fa-facebook-f"></i>
                        </div>

                        <div class="textoContato">
                            <strong> Facebook </strong>
                            <span> Sinalize Amor </span>
                        </div>
                    </a>


                    <!-- EMAIL -->

                    <a href="mailto:seuemail@email.com" class="itemContato">
                        <div class="iconeContato">
                            <i class="fa-solid fa-envelope"></i>
                        </div>

                        <div class="textoContato">
                            <strong> E-mail </strong>
                            <span>seuemail@email.com</span>
                        </div>
                    </a>


                    <!-- TELEFONE -->

                    <a href="tel:+5500000000000" class="itemContato">
                        <div class="iconeContato">
                            <i class="fa-solid fa-phone"></i>
                        </div>

                        <div class="textoContato">
                            <strong> Telefone </strong>
                            <span> (00) 00000-0000 </span>
                        </div>
                    </a>

                </div>
                <!--Fim da lista/bloco para entrar em contato -->

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

            <h2> Precisa de atendimento?</h2>

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

    <h2>Sinalize<span>Amor</span></h2>

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