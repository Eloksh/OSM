<?php
//aggiunta la paginazione della tabella delle morance

$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
isLogged();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php stampaIntestazione(); ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!--  CSS -->
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/statistiche_zona.css">
</head>

<body class="bg-light">
    <div class="container-fluid p-0">
        <?php stampaNavbar(); ?>
        
        <div class="container py-4">
            <div class="d-flex mb-4" style="justify-content: space-around;">
                <a href='statistiche.php' class="btn btn-outline-primary d-flex align-items-center">
                    <span class="me-2">Statistiche Generali</span>
                </a>
                <a href='statistiche_det.php' class="btn btn-outline-primary d-flex align-items-center">
                    <span class="me-2">Dettaglio Statistiche</span>
                    <img src="../img/inserisci2.png" alt="Dettaglio" class="img-fluid" style="max-height: 24px;">
                </a>
            </div>

            <?php
            $util = $config_path .'/../db/db_conn.php';
            require $util;
            ?>

            <div class="card stats-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0 d-flex align-items-center justify-content-center">
                        Statistiche per zona
                        <img src='../img/inserisci2.png' alt="Statistiche" class="ms-2" style="max-height: 30px;">
                    </h2>
                </div>
                <div class="card-body">
                    <form name="form" id="form" action="utility_stat.php" method="post">
                        <div class="form-container">
                            <div class="vertical-line"></div>
                            
                            <div class="form-group-container">
                                <div class="form-group">
                                    <div class="label-container">
                                        <label for="zona_richiesta">Zona</label>
                                    </div>
                                    <select class="form-select custom-select" id="zona_richiesta" name="zona_richiesta">
                                        <option value="nord">nord</option>
                                        <option value="ovest">ovest</option>
                                        <option value="sud">sud</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <div class="label-container">
                                        <label for="valore">Tipo</label>
                                    </div>
                                    <select class="form-select custom-select" id="valore" name="valore">
                                        <option value="maschi">maschi e femmine</option>
                                        <option value="maggiorenni">maggiorenni</option>
                                        <option value="fertili">fertili</option>
                                        <option value="fasce">fasce</option>
                                        <option value="abitanti">numero persone</option>
                                        <option value="matricolati">studenti immatricolati</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="button-container">
                                <button type="submit" class="btn btn-primary" name="invia">
                                    <i class="bi bi-search me-2"></i>Visualizza statistiche
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>