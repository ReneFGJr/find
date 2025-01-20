<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<title>FIND: Catálogo de Biblioteca</title>

<style>
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        flex-direction: column;
        font-family: Arial, sans-serif;
        transition: background-color 0.5s ease;
    }

    .link-container {
        margin-top: auto;
        /* Empurra o conteúdo para cima */
        text-align: right;
        /* Alinha o texto à direita */
        padding: 1px 3px;
    }

    .link-container a {
        text-decoration: none;
        color: #555;
        font-size: 8px;
    }
</style>
<script>
    // Função para gerar cores RGB sequenciais
    function generateColors() {
        let r = 120,
            g = 120,
            b = 120; // Inicia com preto
        return function() {
            if (r < 255) {
                r++;
            } else if (g < 255) {
                g++;
                r = 120;
            } else if (b < 255) {
                b++;
                g = 120;
            } else {
                r = 120;
                g = 120;
                b = 120; // Reinicia ciclo
            }
            return `rgb(${r}, ${g}, ${b})`;
        };
    }

    const nextColor = generateColors(); // Inicializa o gerador
    setInterval(() => {
        document.body.style.backgroundColor = nextColor();
    }, 50); // Altere a cor a cada 50ms
</script>

<body>
    <div style="margin-top: 150px; text-align: center;" class="container">
        <div class="row">
            <div class="col-12">
                <a href="https://www.ufrgs.br/find/app/">
                    <img src="/img/logo_find.png" height="120px" style="border: 0px;">
                </a>
                <h1>Catálogo de Biblioteca</h1>
            </div>
        </div>
    </div>

    <div class="link-container">
        <a href="<?php echo base_url('/tt');?>">tt</a>
    </div>
</body>