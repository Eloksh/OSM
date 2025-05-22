<?php
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/stat.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Statistiche Studenti Matricolati</title>
</head>
<body>
    <?php stampaNavbar(); ?>
    
    <div class="container">
        <div class="row mt-4">
            <div class="col-12">
                <div class="stat-links">
                    <a href='statistiche_det.php' class="btn btn-sm btn-outline-primary">
                        Dettaglio Statistiche <img src="../img/inserisci2.png" alt="Dettaglio" height="20">
                    </a>
                    <a href='statistiche_zona.php' class="btn btn-sm btn-outline-info">
                        Statistiche per zona <i class="fas fa-chart-pie"></i>
                    </a>
                </div>
            </div>
        </div>

        <?php
        $util = $config_path .'/../db/db_conn.php';
        require $util;

        $oraoggi = date("Y/m/d");
        $zona = isset($_GET["zona_richiesta"]) ? $_GET["zona_richiesta"] : 'nord';

        //persone in totale
        $query = "SELECT * FROM persone"; 
        $result = $conn->query($query);
        echo $conn->error ? $conn->error : '';
        $tot_persone = $result ? $result->num_rows : 0;

        // Persone in totale in base alla zona
        $query = "SELECT * FROM persone 
                  INNER JOIN pers_casa ON pers_casa.ID_PERS=persone.ID 
                  INNER JOIN casa ON pers_casa.ID_casa=casa.ID
                  INNER JOIN morance ON casa.ID_moranca=morance.ID
                  INNER JOIN zone ON morance.cod_zona=zone.COD
                  WHERE zone.NOME='$zona'";
        $result = $conn->query($query);
        echo $conn->error;
        if($result) {
            $numero_persone = $result->num_rows;
        }

        // Persone con matricola per zona
        $query = "SELECT count(persone.ID) FROM persone 
                  INNER JOIN pers_casa ON pers_casa.ID_PERS=persone.ID 
                  INNER JOIN casa ON pers_casa.ID_casa=casa.ID
                  INNER JOIN morance ON casa.ID_moranca=morance.ID
                  INNER JOIN zone ON morance.cod_zona=zone.COD
                  WHERE zone.NOME='$zona' AND persone.matricola_stud IS NOT NULL";
        $result = $conn->query($query);
        echo $conn->error;
        if($result) {
            $row = $result->fetch_array();
            $matricolati = $row["count(persone.ID)"];
            $no_matricola = $numero_persone - $matricolati;
        }

        // Media età delle persone 
        $query = "SELECT avg(DATEDIFF(CURDATE(),data_nascita)) as etamedia FROM persone
                  INNER JOIN pers_casa ON pers_casa.ID_PERS=persone.ID 
                  INNER JOIN casa ON pers_casa.ID_casa=casa.ID
                  INNER JOIN morance ON casa.ID_moranca=morance.ID
                  INNER JOIN zone ON morance.cod_zona=zone.COD
                  WHERE zone.NOME='$zona'";
        $result = $conn->query($query);
        echo $conn->error;
        if($result) {
            $row = $result->fetch_array();
            $etamedia = floor($row['etamedia']/365);
        }
        ?>

        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="chart-container">
                    <div id="chartContainer1" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6 mx-auto">
                <div class="stat-info">
                    Età media: <strong><?= (ceil($etamedia*10))/10 ?> anni</strong>
                </div>
                <div class="stat-info">
                    Totale persone nella zona <strong><?= $zona ?>: <?= $numero_persone ?> su <?= $tot_persone ?> </strong>
                </div>
                
                <div class="stat-controls text-center mb-3">
                    <form action="" method="GET" class="form-inline justify-content-center">
                        <div class="form-group">
                            <select name="zona_richiesta" class="form-control mr-2">
                                <option value="nord" <?= $zona == 'nord' ? 'selected' : '' ?>>Nord</option>
                                <option value="ovest" <?= $zona == 'ovest' ? 'selected' : '' ?>>Ovest</option>
                                <option value="sud" <?= $zona == 'sud' ? 'selected' : '' ?>>Sud</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Visualizza</button>
                    </form>
                    
                    <form action="statistiche.php" class="mt-3">
                        <button type="submit" class="btn btn-secondary">Torna</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- CanvasJS -->
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    
    <script>
    window.onload = function() {
        var chart = new CanvasJS.Chart("chartContainer1", {
            animationEnabled: true,
            responsive: true,
            title: {
                text: "STUDENTI MATRICOLATI NELLA ZONA <?php echo strtoupper($zona) ?>",
                fontSize: 22
            },
            legend: {
                fontSize: 16
            },
            exportEnabled: true,
            data: [{
                type: "pie",
                showInLegend: true,
                toolTipContent: "{legendText}: {y}%",
                indexLabel: "{indexLabel}: {y}%",
                indexLabelFontSize: 14,
                yValueFormatString: "##0.00\"%\"",
                dataPoints: [
                    { 
                        y: <?php echo $numero_persone > 0 ? (($matricolati/$numero_persone)*100) : 0 ?>, 
                        legendText: "<?php echo "Matricolati: ".$matricolati ?>", 
                        indexLabel: "Matricolati", 
                        color: "#4E79A7" 
                    }, 
                    { 
                        y: <?php echo $numero_persone > 0 ? (($no_matricola/$numero_persone)*100) : 0 ?>, 
                        legendText: "<?php echo "Non matricolati: ".$no_matricola ?>", 
                        indexLabel: "Non matricolati", 
                        color: "#F28E2B" 
                    }
                ]
            }]
        });
        chart.render();
        
        // Make chart responsive
        function resizeChart() {
            chart.render();
        }
        
        window.addEventListener('resize', resizeChart);
    }
    </script>
</body>
</html>