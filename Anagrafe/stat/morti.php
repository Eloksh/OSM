<?php
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
isLogged();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php stampaIntestazione(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/stat.css">
    <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <?php stampaNavbar(); ?>
    
    <div class="container">
        <?php
        $util = $config_path .'/../db/db_conn.php';
        require $util;

        $oraoggi = date("Y/m/d");

        // Persone morte in totale
        $query = "SELECT count(Tab1.NOMI) as MORTI 
        from (
            select persone.ID as Indice, persone.NOMINATIVO as NOMI from persone 
            where persone.DATA_MORTE is not null 
        ) as Tab1;";  
        $result = $conn->query($query);
        echo $conn->error ;
        
        if($result) {
            $row = $result->fetch_array();
            $morti_totali = $row["MORTI"];
        }

        // Numero morti tra 0 e 1 anno
        $query = "SELECT count(Tab1.NOMI) as MORTI 
        from (
            select persone.ID as Indice, persone.NOMINATIVO as NOMI from persone 
            where persone.DATA_MORTE is not null and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)>0 and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)<= 365
        ) as Tab1;";  
        $result = $conn->query($query);
        echo $conn->error;
        
        if($result) {
            $row = $result->fetch_array();
            $morti_0_1 = $row["MORTI"];
        }

        // Morti tra 1 e 5 anni
        $query = "SELECT count(Tab1.NOMI) as MORTI 
        from (
            select persone.ID as Indice, persone.NOMINATIVO as NOMI from persone 
            where persone.DATA_MORTE is not null and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)>365 and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)<= 1825
        ) as Tab1;"; 
        $result = $conn->query($query);
        echo $conn->error;
        
        if($result) {
            $row = $result->fetch_array();
            $morti_1_5 = $row["MORTI"];
        }

        // Morti tra 5 e 10 anni
        $query = "SELECT count(Tab1.NOMI) as MORTI 
        from (
            select persone.ID as Indice, persone.NOMINATIVO as NOMI from persone 
            where persone.DATA_MORTE is not null and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)>1825 and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)<= 3650
        ) as Tab1;"; 
        $result = $conn->query($query);
        echo $conn->error;
        
        if($result) {
            $row = $result->fetch_array();
            $morti_5_10 = $row["MORTI"];
        }

        // Morti tra 10 e 20 anni
        $query = "SELECT count(NOMI) as MORTI 
        from (
            select persone.ID as Indice, persone.NOMINATIVO as NOMI from persone 
            where persone.DATA_MORTE is not null and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)>3650 and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)<= 7300
        ) as Tab1;"; 
        $result = $conn->query($query);
        echo $conn->error;
        
        if($result) {
            $row = $result->fetch_array();
            $morti_10_20 = $row["MORTI"];
        }

        // Morti tra 20 e 40 anni
        $query = "SELECT count(tab1.NOMI) as MORTI
        from (
            select persone.ID as Indice, persone.NOMINATIVO as NOMI from persone 
            where persone.DATA_MORTE is not null and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)>7300 and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)<= 14600
        ) as tab1;"; 
        $result = $conn->query($query);
        echo $conn->error;
        
        if($result) {
            $row = $result->fetch_array();
            $morti_20_40 = $row["MORTI"];
        }

        // Morti tra 40 e 60 anni
        $query = "SELECT count(Tab1.NOMI) as MORTI 
        from (
            select persone.ID as Indice, persone.NOMINATIVO as NOMI from persone 
            where persone.DATA_MORTE is not null and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)>14600 and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)<= 21900
        ) as Tab1;"; 
        $result = $conn->query($query);
        echo $conn->error;
        
        if($result) {
            $row = $result->fetch_array();
            $morti_40_60 = $row["MORTI"];
        }

        // Morti oltre 60 anni
        $query = "SELECT count(Tab1.NOMI) as MORTI 
        from ( 
            select persone.ID as Indice, persone.NOMINATIVO as NOMI from persone 
            where persone.DATA_MORTE is not null and DATEDIFF(persone.DATA_MORTE,persone.DATA_NASCITA)>21900  
        ) as Tab1;"; 
        $result = $conn->query($query);
        echo $conn->error;
        
        if($result) {
            $row = $result->fetch_array();
            $morti_60 = $row["MORTI"];
        }

        $anno_corrente = date("yy");

        // Media età delle persone 
        $query = "select avg(DATEDIFF('$oraoggi',data_nascita)) from persone";
        $result = $conn->query($query);
        echo $conn->error;
        
        if($result) {
            $row = $result->fetch_array();
            $etamedia = floor(($row["avg(DATEDIFF('$oraoggi',data_nascita))"]/365));
        }
        ?>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-body">
                        <h2 class="stat-title">MORTALITÀ PER FASCE DI ETÀ</h2>
                        <div id="chartContainer1" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card stat-controls">
                    <div class="card-body">
                        <div class="stat-info">
                            Età media: <?php echo (ceil($etamedia*10))/10; ?>
                        </div>
                        
                        <div class="text-center mt-3">
                            <form action="statistiche.php">
                                <button type="submit" class="btn btn-primary">TORNA</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var chart = new CanvasJS.Chart("chartContainer1", {
            animationEnabled: true,
            exportEnabled: true,
            responsive: true,
            maintainAspectRatio: false,
            title: {
                text: "MORTALITÀ PER FASCE DI ETÀ",
                fontFamily: "'Arial', sans-serif",
                fontSize: 24,
                fontWeight: "normal"
            },
            legend: {
                cursor: "pointer",
                itemclick: toggleDataSeries
            },
            data: [{
                type: "pie",
                showInLegend: true,
                toolTipContent: "{label}: {y}%",
                yValueFormatString: "##0.00\"%\"",
                indexLabel: "{label}: {y}%",
                dataPoints: [
                    { y: <?php echo (($morti_0_1/$morti_totali)*100) ?>, legendText: "Neonatale (<?php echo $morti_0_1; ?>)", label: "0-1 anno" },
                    { y: <?php echo (($morti_1_5/$morti_totali)*100) ?>, legendText: "1-5 anni (<?php echo $morti_1_5; ?>)", label: "1-5 anni" },
                    { y: <?php echo (($morti_5_10/$morti_totali)*100) ?>, legendText: "5-10 anni (<?php echo $morti_5_10; ?>)", label: "5-10 anni" },
                    { y: <?php echo (($morti_10_20/$morti_totali)*100) ?>, legendText: "10-20 anni (<?php echo $morti_10_20; ?>)", label: "10-20 anni" },
                    { y: <?php echo (($morti_20_40/$morti_totali)*100) ?>, legendText: "20-40 anni (<?php echo $morti_20_40; ?>)", label: "20-40 anni" },
                    { y: <?php echo (($morti_40_60/$morti_totali)*100) ?>, legendText: "40-60 anni (<?php echo $morti_40_60; ?>)", label: "40-60 anni" },
                    { y: <?php echo (($morti_60/$morti_totali)*100) ?>, legendText: "60+ anni (<?php echo $morti_60; ?>)", label: "60+ anni" }
                ]
            }]
        });
        
        chart.render();
        
        function toggleDataSeries(e) {
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else {
                e.dataSeries.visible = true;
            }
            chart.render();
        }
        
        // Handle responsive resizing
        window.addEventListener('resize', function() {
            chart.render();
        });
    });
    </script>
</body>
</html>