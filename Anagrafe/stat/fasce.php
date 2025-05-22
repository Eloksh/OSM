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
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/stat.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Statistiche Fasce d'Età</title>
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

        // Persone in totale
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

        // 7300 giorni = 20 anni = 365 * 20
        // Persone con età minore di 20 anni
        $query = "SELECT count(persone.id) as indice FROM persone 
                  INNER JOIN pers_casa ON pers_casa.ID_PERS=persone.ID 
                  INNER JOIN casa ON pers_casa.ID_casa=casa.ID
                  INNER JOIN morance ON casa.ID_moranca=morance.ID
                  INNER JOIN zone ON morance.cod_zona=zone.COD
                  WHERE zone.NOME='$zona' AND DATEDIFF('$oraoggi',data_nascita)>=0 AND DATEDIFF('$oraoggi',data_nascita)<= 7300";
        $result = $conn->query($query);
        echo $conn->error;
        if($result) {
            $row = $result->fetch_array();
            $minori20 = $row["indice"];
        }

        // Persone con età tra i 20 e i 40
        $query = "SELECT count(persone.id) as indice FROM persone 
                  INNER JOIN pers_casa ON pers_casa.ID_PERS=persone.ID 
                  INNER JOIN casa ON pers_casa.ID_casa=casa.ID
                  INNER JOIN morance ON casa.ID_moranca=morance.ID
                  INNER JOIN zone ON morance.cod_zona=zone.COD
                  WHERE zone.NOME='$zona' AND DATEDIFF('$oraoggi',data_nascita)>7300 AND DATEDIFF('$oraoggi',data_nascita)<= 14600";
        $result = $conn->query($query);
        echo $conn->error;
        if($result) {
            $row = $result->fetch_array();
            $persone20_40 = $row["indice"];
        }

        // Persone tra 40 e 60
        $query = "SELECT count(persone.id) as indice FROM persone 
                  INNER JOIN pers_casa ON pers_casa.ID_PERS=persone.ID 
                  INNER JOIN casa ON pers_casa.ID_casa=casa.ID
                  INNER JOIN morance ON casa.ID_moranca=morance.ID
                  INNER JOIN zone ON morance.cod_zona=zone.COD
                  WHERE zone.NOME='$zona' AND DATEDIFF('$oraoggi',data_nascita)>14600 AND DATEDIFF('$oraoggi',data_nascita)<= 21900";
        $result = $conn->query($query);
        echo $conn->error;
        if($result) {
            $row = $result->fetch_array();
            $persone_40_60 = $row["indice"];
        }

        // Persone con età maggiore di 60 anni
        $query = "SELECT count(persone.id) as indice FROM persone 
                  INNER JOIN pers_casa ON pers_casa.ID_PERS=persone.ID 
                  INNER JOIN casa ON pers_casa.ID_casa=casa.ID
                  INNER JOIN morance ON casa.ID_moranca=morance.ID
                  INNER JOIN zone ON morance.cod_zona=zone.COD
                  WHERE zone.NOME='$zona' AND DATEDIFF('$oraoggi',data_nascita)>21900";
        $result = $conn->query($query);
        if($result) {
            $row = $result->fetch_array();
            $maggiori60 = $row["indice"];
        }
        
        $sprovvisti = $numero_persone - ($minori20 + $persone20_40 + $persone_40_60 + $maggiori60);
        
        $anno_corrente = date("Y");
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
                text: "PERSONE PER FASCE DI ETÀ",
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
                    { 
                        y: <?php echo $numero_persone > 0 ? (($minori20/$numero_persone)*100) : 0 ?>, 
                        legendText: "<?php echo "fino 20 anni: ".$minori20 ?>", 
                        label: "fino 20 anni", 
                        color: "#4E79A7" 
                    }, 
                    { 
                        y: <?php echo $numero_persone > 0 ? (($persone20_40/$numero_persone)*100) : 0 ?>, 
                        legendText: "<?php echo "20 / 40 anni: ".$persone20_40 ?>", 
                        label: "20 - 40 anni", 
                        color: "#F28E2B" 
                    },
                    { 
                        y: <?php echo $numero_persone > 0 ? (($persone_40_60/$numero_persone)*100) : 0 ?>, 
                        legendText: "<?php echo "40 / 60 anni: ".$persone_40_60 ?>", 
                        label: "40 - 60 anni", 
                        color: "#E15759" 
                    },
                    { 
                        y: <?php echo $numero_persone > 0 ? (($maggiori60/$numero_persone)*100) : 0 ?>, 
                        legendText: "<?php echo "60 o più: ".$maggiori60 ?>", 
                        label: "sopra 60 anni", 
                        color: "#76B7B2" 
                    },
                    { 
                        y: <?php echo $numero_persone > 0 ? (($sprovvisti/$numero_persone)*100) : 0 ?>, 
                        legendText: "<?php echo "senza età: ".$sprovvisti ?>", 
                        label: "senza età", 
                        color: "#59A14F" 
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