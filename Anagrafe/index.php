<!DOCTYPE html>
<html lang="it">
<?php
$config_path = __DIR__;
$util = $config_path .'/util.php';
require $util;
setup();
?>

<head>
    <?php stampaIntestazione(); ?>
    <!-- Bootstrap  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS  -->
    <link rel="stylesheet" href="/OSM/Anagrafe/css/index.css">
</head>

<body>
    <?php stampaNavbar(); ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <?php 
                if(isset($_GET['welcome'])) {
                    echo '<div class="alert alert-success welcome-alert" role="alert">';
                    echo "<h2>Benvenuto {$_SESSION['nome']}</h2>";
                    echo '</div>';
                }
                ?>
                
                <div class="flag-container my-4">
                    <img src="img/bandiera.png" class="img-fluid flag-img" alt="Bandiera" style="max-width: 200px;">
                </div>
                
                <div class="main-title mb-4">
                    <h1 class="display-5">Anagrafe Web e Mappa OpenStreetMap di moranças, case e strade <br> del villaggio Nague (Guinea Bissau)</h1>
                    <h2 class="subtitle">Progetto rimodernizzato da <strong>Eloksh Basel e Codreanu Stefan</strong> </h2>
                </div>
                
                <div class="main-image-container mt-4 mb-5">
                    <img src="img/schermata.jpg" class="img-fluid main-image rounded shadow" alt="Screenshot del progetto">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="text-muted mb-0">© <?php echo date('Y'); ?> - Progetto OSM Nague - Eloksh Basel & Codreanu Stefan 
                                                                           <br> <strong>Scuola IIS. A.Avogadro</strong> </p>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
