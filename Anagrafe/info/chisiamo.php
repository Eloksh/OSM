<!DOCTYPE html>
<html lang="it">
<?php
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
unsetPag(basename(__FILE__)); 
?>
<head>
    <?php stampaIntestazione(); ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS personalizzato -->
    <link rel="stylesheet" href="../css/chi-siamo.css">
</head>
<body>
    <?php stampaNavbar(); ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <h1 class="page-title mb-5">Chi siamo</h1>
            </div>
        </div>
        
        <div class="row justify-content-center mb-4 gallery-row">
            <div class="col-md-5 mb-4">
                <div class="gallery-item">
                    <img src="../img/gruppo.jpeg" class="img-fluid rounded" alt="Il nostro gruppo">
                </div>
            </div>
            <div class="col-md-5 mb-4">
                <div class="gallery-item">
                    <img src="../img/lavoro2.jpeg" class="img-fluid rounded" alt="Lavoro di gruppo">
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center mb-4 gallery-row">
            <div class="col-md-5 mb-4">
                <div class="gallery-item">
                    <img src="../img/lavoro3.jpeg" class="img-fluid rounded" alt="Fase di progettazione">
                </div>
            </div>
            <div class="col-md-5 mb-4">
                <div class="gallery-item">
                    <img src="../img/lavoro4.jpeg" class="img-fluid rounded" alt="Sviluppo del progetto">
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center mb-4 gallery-row">
            <div class="col-md-5 mb-4">
                <div class="gallery-item">
                    <img src="../img/Nague1.jpg" class="img-fluid rounded" alt="Pozzo Nague">
                </div>
            </div>
            <div class="col-md-5 mb-4">
                <div class="gallery-item">
                    <img src="../img/Nague2.jpg" class="img-fluid rounded" alt="Pozzo di Nague">
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center mb-5 gallery-row">
            <div class="col-md-5 mb-4">
                <div class="gallery-item">
                    <img src="../img/Nague3.jpg" class="img-fluid rounded" alt="Villaggio di  Nague">
                </div>
            </div>
            <div class="col-md-5 mb-4">
                <div class="gallery-item">
                    <img src="../img/Nague4.jpg" class="img-fluid rounded" alt="Vita a Nague">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>