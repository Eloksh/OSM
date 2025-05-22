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
    <title>Statistiche Abitanti per Zona</title>
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

        // Media età delle persone 
        $query = "SELECT avg(DATEDIFF(CURDATE(),data_nascita)) as etamedia FROM persone";
        $result = $conn->query($query);
        echo $conn->error;
        if($result) {
            $row = $result->fetch_array();
            $etamedia = floor($row['etamedia']/365);
        }

        // Persone in totale per zona
        $query = "SELECT zone.NOME as zona, count(persone.id) as numero FROM persone 
                  INNER JOIN pers_casa ON pers_casa.ID_PERS=persone.ID 
                  INNER JOIN casa ON pers_casa.ID_casa=casa.ID
                  INNER JOIN morance ON casa.ID_moranca=morance.ID
                  INNER JOIN zone ON morance.cod_zona=zone.COD
                  GROUP BY zone.NOME";
        $result = $conn->query($query);
        echo $conn->error;
        
        $elenco = array();
        if($result) {
            while($row = $result->fetch_array()) {
                $elenco[$row["zona"]] = $row["numero"];
            }
        }

        $anno_corrente = date("Y");
        
        // Calculate total population
        $popolazione_totale = array_sum($elenco);
        ?>

        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="chart-container">
                    <div id="chartContainer1" style="height: 500px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6 mx-auto">
                <div class="stat-info">
                    Età media: <strong><?= (ceil($etamedia*10))/10 ?> anni</strong>
                </div>
                <div class="stat-info">
                    Popolazione totale: <strong><?= $popolazione_totale ?> persone</strong>
                </div>
                
                <div class="stat-controls text-center mb-3">
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
                text: "DISTRIBUZIONE ABITANTI PER ZONA",
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
                indexLabel: "{label}: {y}%",
                indexLabelFontSize: 14,
                yValueFormatString: "##0.00\"%\"",
                dataPoints: [
                    <?php 
                    $colors = ['#4E79A7', '#F28E2B', '#E15759', '#76B7B2', '#59A14F', '#EDC948'];
                    $i = 0;
                    foreach($elenco as $zona => $numero) {
                        $percentuale = $popolazione_totale > 0 ? (($numero/$popolazione_totale)*100) : 0;
                        echo "{ 
                            y: $percentuale, 
                            legendText: \"$zona: $numero\", 
                            label: \"$zona\", 
                            color: \"".$colors[$i % count($colors)]."\"
                        }";
                        $i++;
                        if($i < count($elenco)) echo ", ";
                    }
                    ?>
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