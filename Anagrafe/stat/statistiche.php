<?php
//aggiunta la paginazione della tabella delle morance

$config_path = __DIR__;
$util = $config_path . '/../util.php';
require $util;
setup();
isLogged();
?>

<!DOCTYPE html>
<html lang="it">
<?php stampaIntestazione(); ?>

<head>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/dati.css">
</head>

<body class="bg-light">
    <?php stampaNavbar(); ?>

    <?php
    $util = $config_path . '/../db/db_conn.php';
    require $util;


    // età media della popolazione
    $query = "SELECT AVG(TIMESTAMPDIFF(YEAR, DATA_NASCITA, CURDATE())) AS eta_media FROM persone WHERE DATA_NASCITA IS NOT NULL";
    $result = $conn->query($query);
    echo $conn->error;
    if ($result) {
        $row = $result->fetch_array();
        $etamedia = floor($row["eta_media"]);
    }

    // persone in totale
    $query = "SELECT * from persone";
    $result = $conn->query($query);
    if ($result) {
        $numero_persone = $result->num_rows;
    }

    // persone minorenni sul totale
    $oraoggi = date("Y/m/d");
    $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)<6570 ";   //6570 è il numero di giorni che una persona vive fino a 18 anni
    $result = $conn->query($query);
    echo $conn->error;

    if ($result) {
        $row = $result->fetch_array();
        $minorenni = $row["count(id)"];
        $maggiorenni = $numero_persone - $minorenni;
    }

    // abitanti in ogni casa
    $query = "SELECT count(id) from casa";   //conta il numero di case
    $result = $conn->query($query);
    echo $conn->error;

    if ($result) {
        $row = $result->fetch_array();
        $persone_casa = $numero_persone / $row["count(id)"];
    }

    // persone con età maggiore di 40 anni
    $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)>14600";
    $result = $conn->query($query);
    echo $conn->error;
    if ($result) {
        $row = $result->fetch_array();
        $maggiori40 = $row["count(id)"];
    }

    // persone di sesso maschile
    $query = "SELECT * from persone where sesso='m' ";
    $result = $conn->query($query);
    echo $conn->error;
    if ($result) {
        $numero_persone_m = $result->num_rows;
    }

    // persone di sesso femminile
    $query = "SELECT * from persone where sesso='f' ";
    $result = $conn->query($query);
    echo $conn->error;
    if ($result) {
        $numero_persone_f = $result->num_rows;
    }

    // donne in età fertile (si intende tra 13 e 40 anni)
    $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)>4745 and DATEDIFF('$oraoggi',data_nascita)<14600 and sesso='f' ";
    $result = $conn->query($query);
    echo $conn->error;
    if ($result) {
        $row = $result->fetch_array();
        $etafertile = $row["count(id)"];
        $nonfertile = $numero_persone_f - $etafertile;
    }

    // persone con età minore di 20 anni
    $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)>=0 and DATEDIFF('$oraoggi',data_nascita)<= 7300";
    $result = $conn->query($query);
    echo $conn->error;
    if ($result) {
        $row = $result->fetch_array();
        $minori20 = $row["count(id)"];
    }

    // persone con età tra i 20 e i 40
    $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)>7300 and DATEDIFF('$oraoggi',data_nascita)<=14600";
    $result = $conn->query($query);
    echo $conn->error;
    if ($result) {
        $row = $result->fetch_array();
        $persone20_40 = $row["count(id)"];
    }

    // persone tra 40 e 60
    $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)>14600 and DATEDIFF('$oraoggi',data_nascita)<=21900";
    $result = $conn->query($query);
    echo $conn->error;
    if ($result) {
        $row = $result->fetch_array();
        $persone_40_60 = $row["count(id)"];
    }

    // persone morte 
    $query = "SELECT count(Tab1.Indice) as MORTI 
from (( 
select persone.ID as Indice,persone.NOMINATIVO as NOMI from persone where persone.DATA_MORTE is not null 
UNION select persone_sto.ID as ID,persone_sto.NOMINATIVO as NOMI from persone_sto where persone_sto.DATA_MORTE is not null 
) as Tab1);";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_array();
        $morti = $row["MORTI"];
    }

    // persone con età maggiore di 60 anni
    $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)>21900";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_array();
        $maggiori60 = $row["count(id)"];
    }
    $sprovvisti = $numero_persone - ($minori20 + $persone20_40 + $persone_40_60 + $maggiori60);

    $anno_corrente = date("yy");
    ?>

    <div class="container">
        <div class="stats-header text-center">
            <h1><i class="fa fa-chart-pie"></i> Villaggio di Nague: Statistiche <i class="fa fa-chart-pie"></i></h1>
            <p class="lead">Analisi demografica della popolazione</p>
        </div>

        <div class="action-buttons">
            <a href='statistiche_det.php' class="btn btn-primary btn-stat">
                <i class="fa fa-info-circle"></i> Dettaglio Statistiche
            </a>
            <a href='statistiche_zona.php' class="btn btn-success btn-stat">
                <i class="fa fa-chart-pie"></i> Statistiche per zona
            </a>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="chart-container" id="chartContainer1" style="height: 300px;"></div>
            </div>
            <div class="col-md-6">
                <div class="chart-container" id="chartContainer2" style="height: 300px;"></div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="chart-container" id="chartContainer3" style="height: 300px;"></div>
            </div>
            <div class="col-md-6">
                <div class="chart-container" id="chartContainer4" style="height: 300px;"></div>
            </div>
        </div>

        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Riassunto Dati Demografici</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Popolazione Totale</h5>
                                        <p class="card-text display-4"><?php echo $numero_persone; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Età Media</h5>
                                        <p class="card-text display-4"><?php echo $etamedia; ?> anni</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Abitanti per Casa</h5>
                                        <p class="card-text display-4"><?php echo round($persone_casa, 1); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- CanvasJS for charts -->
    <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    <!-- Charts scripts -->
    <script>
        window.onload = function() {
            var chart1 = new CanvasJS.Chart("chartContainer1", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "PERCENTUALI MASCHI E FEMMINE",
                    fontSize: 18
                },
                exportEnabled: true,
                data: [{
                    type: "pie",
                    showInLegend: true,
                    legendText: "{legendText}",
                    toolTipContent: "{label}: <strong>{y}%</strong>",
                    indexLabel: "{label} {y}%",
                    dataPoints: [{
                            y: <?php echo (ceil(($numero_persone_f / $numero_persone) * 100)) ?>,
                            legendText: "Femmine (<?php echo $numero_persone_f ?>)",
                            label: "Femmine",
                            color: "#FF9CAB"
                        },
                        {
                            y: <?php echo (floor(($numero_persone_m / $numero_persone) * 100)) ?>,
                            legendText: "Maschi (<?php echo $numero_persone_m ?>)",
                            label: "Maschi",
                            color: "#5DA5DA"
                        }
                    ]
                }]
            });
            chart1.render();

            var chart2 = new CanvasJS.Chart("chartContainer2", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "PERCENTUALE MINORENNI E MAGGIORENNI",
                    fontSize: 18
                },
                exportEnabled: true,
                data: [{
                    type: "pie",
                    showInLegend: true,
                    legendText: "{legendText}",
                    toolTipContent: "{label}: <strong>{y}%</strong>",
                    indexLabel: "{label} {y}%",
                    dataPoints: [{
                            y: <?php echo round(($minorenni / $numero_persone) * 100, 2) ?>,
                            legendText: "Minorenni (<?php echo $minorenni ?>)",
                            label: "Minorenni",
                            color: "#FAA43A"
                        },
                        {
                            y: <?php echo round(($maggiorenni / $numero_persone) * 100, 2) ?>,
                            legendText: "Maggiorenni (<?php echo $maggiorenni ?>)",
                            label: "Maggiorenni",
                            color: "#60BD68"
                        }
                    ]
                }]
            });
            chart2.render();

            var chart3 = new CanvasJS.Chart("chartContainer3", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "PERSONE PER FASCE DI ETÀ",
                    fontSize: 18
                },
                exportEnabled: true,
                data: [{
                    type: "pie",
                    showInLegend: true,
                    legendText: "{legendText}",
                    toolTipContent: "{label}: <strong>{y}%</strong>",
                    indexLabel: "{label} {y}%",
                    dataPoints: [{
                            y: <?php echo round(($minori20 / $numero_persone) * 100, 2) ?>,
                            legendText: "Fino 20 anni (<?php echo $minori20 ?>)",
                            label: "< 20 anni",
                            color: "#5DA5DA"
                        },
                        {
                            y: <?php echo round(($persone20_40 / $numero_persone) * 100, 2) ?>,
                            legendText: "20-40 anni (<?php echo $persone20_40 ?>)",
                            label: "20-40 anni",
                            color: "#F17CB0"
                        },
                        {
                            y: <?php echo round(($persone_40_60 / $numero_persone) * 100, 2) ?>,
                            legendText: "40-60 anni (<?php echo $persone_40_60 ?>)",
                            label: "40-60 anni",
                            color: "#60BD68"
                        },
                        {
                            y: <?php echo round(($maggiori60 / $numero_persone) * 100, 2) ?>,
                            legendText: "60+ anni (<?php echo $maggiori60 ?>)",
                            label: "60+ anni",
                            color: "#B276B2"
                        },
                        {
                            y: <?php echo round(($sprovvisti / $numero_persone) * 100, 2) ?>,
                            legendText: "Senza età (<?php echo $sprovvisti ?>)",
                            label: "Senza età",
                            color: "#DECF3F"
                        }
                    ]
                }]
            });
            chart3.render();

            var chart4 = new CanvasJS.Chart("chartContainer4", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "DONNE IN ETÀ FERTILE (13-40 anni)",
                    fontSize: 18
                },
                exportEnabled: true,
                data: [{
                    type: "pie",
                    showInLegend: true,
                    legendText: "{legendText}",
                    toolTipContent: "{label}: <strong>{y}%</strong>",
                    indexLabel: "{label} {y}%",
                    dataPoints: [{
                            y: <?php echo round(($etafertile / $numero_persone_f) * 100, 2) ?>,
                            legendText: "Età fertile (<?php echo $etafertile ?>)",
                            label: "Età fertile",
                            color: "#F17CB0"
                        },
                        {
                            y: <?php echo round(($nonfertile / $numero_persone_f) * 100, 2) ?>,
                            legendText: "Non in età fertile (<?php echo $nonfertile ?>)",
                            label: "Non fertile",
                            color: "#B2912F"
                        }
                    ]
                }]
            });
            chart4.render();
        }
    </script>
</body>
</html>