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
    <title>Statistiche Genere</title>
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
                        Statistiche per zona <i class="fa fa-pie-chart" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        <?php
        $util = $config_path .'/../db/db_conn.php';
        require $util;

        // Default zona if not specified
        $zona = isset($_GET['zona_richiesta']) ? $_GET['zona_richiesta'] : 'nord';

        //persone in totale
        $query = "SELECT * FROM persone"; 
        $result = $conn->query($query);
        echo $conn->error ? $conn->error : '';
        $tot_persone = $result ? $result->num_rows : 0;

        // Persone in totale
        $query = "SELECT * from persone
        inner join pers_casa on pers_casa.ID_PERS=persone.ID 
        inner join casa on pers_casa.ID_casa=casa.ID
        inner join morance on casa.ID_moranca=morance.ID
        inner join zone on morance.cod_zona=zone.COD
        where zone.NOME='$zona' ";
        $result = $conn->query($query);
        
        echo $conn->error ? $conn->error."." : "";
        $numero_persone = $result ? $result->num_rows : 0;

        // Persone di sesso femminile
        $query = "SELECT * from persone 
        inner join pers_casa on pers_casa.ID_PERS=persone.ID 
        inner join casa on pers_casa.ID_casa=casa.ID
        inner join morance on casa.ID_moranca=morance.ID
        inner join zone on morance.cod_zona=zone.COD
        where persone.sesso='f' and zone.NOME='$zona'";
        $result = $conn->query($query);
        
        echo $conn->error ? $conn->error : "";
        $numero_persone_f = $result ? $result->num_rows : 0;

        // Persone di sesso maschile
        $query = "SELECT * from persone 
        inner join pers_casa on pers_casa.ID_PERS=persone.ID 
        inner join casa on pers_casa.ID_casa=casa.ID
        inner join morance on casa.ID_moranca=morance.ID
        inner join zone on morance.cod_zona=zone.COD
        where persone.sesso='m' and zone.NOME='$zona'";
        $result = $conn->query($query);
        
        echo $conn->error ? $conn->error : "";
        $numero_persone_m = $result ? $result->num_rows : 0;

        // Media età delle persone 
        $query = "SELECT avg(DATEDIFF(CURDATE(),data_nascita)) as etamedia from persone
        inner join pers_casa on pers_casa.ID_PERS=persone.ID 
        inner join casa on pers_casa.ID_casa=casa.ID
        inner join morance on casa.ID_moranca=morance.ID
        inner join zone on morance.cod_zona=zone.COD
        where zone.NOME='$zona'";
        $result = $conn->query($query);
        
        echo $conn->error ? $conn->error : "";
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
                    Persone Totali a <?=  $zona ?>: <strong><?=  $numero_persone ?>  su <?=  $tot_persone ?> persone</strong>
                </div>
                
                <div class="stat-controls text-center mb-3">
                    <form action="" method="GET" class="form-inline justify-content-center">
                    <div class="form-group">
                    <div class="north-indicator">
                    </div>
                    <select name="zona_richiesta" class="form-control mr-2">
                        <option value="nord" <?= $zona == 'nord' ? 'selected' : '' ?>>Nord</option>
                        <option value="ovest" <?= $zona == 'ovest' ? 'selected' : '' ?>>Ovest</option>
                        <option value="sud" <?= $zona == 'sud' ? 'selected' : '' ?>>Sud</option>
                    </select>
                       </div>
                        <button type="submit" class="btn btn-primary">Visualizza</button>
                    </form>
                    
                    <form action="statistiche.php" class="mt-3">
                        <button type="submit" class="btn btn-secondary">Torna Indietro</button>
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
                text: "PERCENTUALE DI DONNE E MASCHI NELLA ZONA <?php echo strtoupper($zona) ?>",
                fontSize: 22
            },
            legend: {
                fontSize: 16
            },
            exportEnabled: true,
            data: [{
                type: "pie",
                showInLegend: true,
                toolTipContent: "{label}: {y}%",
                indexLabel: "{label} {y}%",
                indexLabelFontSize: 14,
                yValueFormatString: "##0.00\"%\"",
                dataPoints: [
                    { 
                        y: <?php echo $numero_persone > 0 ? (ceil(($numero_persone_f/$numero_persone)*100)) : 0 ?>, 
                        legendText: "<?php echo "Femmine: ".$numero_persone_f ?>", 
                        label: "Femmine", 
                        color: "#FF7F8F" 
                    }, 
                    { 
                        y: <?php echo $numero_persone > 0 ? (floor(($numero_persone_m/$numero_persone)*100)) : 0 ?>, 
                        legendText: "<?php echo "Maschi: ".$numero_persone_m ?>", 
                        label: "Maschi", 
                        color: "#2E86C1" 
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