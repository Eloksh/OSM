<?php
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
unsetPag(basename(__FILE__)); 
?>
<!DOCTYPE html>
<html lang="it">
<?php stampaIntestazione(); ?>
<head>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add custom CSS -->
    <link rel="stylesheet" href="../css/progetto.css">
    <!-- Add FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php stampaNavbar(); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card project-card mb-5">
                <div class="card-body text-center">
                    <h1 class="display-4 mb-4">Il progetto</h1>
                    
                    <h3 class="mb-4">Guarda il video di presentazione del progetto</h3>
                    
                    <div class="ratio ratio-16x9 mb-4 video-container">
                        <iframe src="https://www.youtube.com/embed/lj0iqdUjjAA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            
            <div class="card article-card mb-5">
                <div class="card-body text-center">
                    <h3 class="mb-4">Leggi gli articoli sul progetto</h3>
                    
                    <div class="row article-links mb-4">
                        <div class="col-md-4 mb-3">
                            <a href="https://wiki.openstreetmap.org/wiki/OsmGuineaBissau_Avogadro" target="_blank" class="article-link">
                                <img src="../img/Openstreetmap_logo.png" alt="la nostra pagina Wiki" class="img-fluid article-img">
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="https://www.storiedialternanza.it/" class="article-link">
                                <img src="../img/logo_alternanza.png" alt="concorso storiedialternanza" class="img-fluid article-img">
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="https://www.wikimedia.it/classi-resistenti-e-progetti-wikimedia-da-torino-alla-guinea-bissau-con-openstreetmap/" target="_blank" class="article-link">
                                <img src="../img/wikimedia.png" alt="dicono di noi" class="img-fluid article-img">
                            </a>
                        </div>
                    </div>
                    
                    <div class="row article-links">
                    <div class="col-md-4 mb-3">
                            <a href="https://www.diregiovani.it/2020/04/20/309317-torino-studenti-creano-anagrafe-digitale-per-guinea-bissau.dg/" target="_blank" class="article-link">
                                <img src="../img/diregiovani.png" alt="dicono di noi" class="img-fluid article-img">
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="https://foss4g-it2020.gfoss.it/programm" class="article-link">
                                <img src="../img/logo_foss4G-IT_2020.png" alt="FOSS4G" class="img-fluid article-img">
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="sintesi_OSMGB.pdf" download class="article-link">
                                <img src="../img/pdf.jpg" alt="Scarica PDF" class="img-fluid article-img">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>