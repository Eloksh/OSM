<?php
//aggiunta la paginazione della tabella delle morance

$config_path = __DIR__;
$util = $config_path . '/../util.php';
require $util;
setup();
isLogged();
?>

<!DOCTYPE html>

<!-- QUERY PER :  ETà MEDIA DELLA POPOLAZIONE 
SELECT 
    AVG(TIMESTAMPDIFF(YEAR, DATA_NASCITA, CURDATE())) AS eta_media
FROM persone
WHERE DATA_NASCITA IS NOT NULL; 
-->

<!-- QUERY PER :  Morti totali 
SELECT 
    COUNT(*) AS numero_deceduti
FROM persone
WHERE DATA_morte IS NOT NULL; 
-->

<html lang="it">
<?php stampaIntestazione(); ?>

<head>
    <!--  Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!--  CSS -->
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/statistiche_det.css">
</head>

<body class="bg-light">
    <?php stampaNavbar(); ?>
    
    <div class="container mt-4">
        <div class="stats-header text-center">
            <h1><i class="fa fa-chart-bar"></i> Dettaglio Statistiche <i class="fa fa-chart-bar"></i></h1>
            <p class="lead">Analisi demografica dettagliata</p>
        </div>
        
        <div class="action-buttons">
            <a href='morti.php' class="btn btn-primary btn-stat">
                <i class="fa fa-info-circle"></i> Statistiche decessi
            </a>
            <a href='statistiche_zona.php' class="btn btn-success btn-stat">
                <i class="fa fa-chart-pie"></i> Statistiche per zona
            </a>
        </div>

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
        $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)<6570 ";
        $result = $conn->query($query);
        echo $conn->error;

        if ($result) {
            $row = $result->fetch_array();
            $minorenni = $row["count(id)"];
            $maggiorenni = $numero_persone - $minorenni;
        }

        // abitanti in ogni casa
        $query = "SELECT count(id) from casa";
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
        
        // età media della popolazione
        $query = "SELECT AVG(TIMESTAMPDIFF(YEAR, DATA_NASCITA, CURDATE())) AS eta_media FROM persone WHERE DATA_NASCITA IS NOT NULL";
        $result = $conn->query($query);
        echo $conn->error;
        if ($result) {
            $row = $result->fetch_array();
            $etamedia = floor($row["eta_media"]);
        }


        // donne in età fertile 
        $query = "SELECT count(id) from persone where DATEDIFF('$oraoggi',data_nascita)>5475 and DATEDIFF('$oraoggi',data_nascita)<16425 and sesso='f' ";
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

        $anno_corrente = date("Y");
        ?>
<!-- Statistiche per anno -->
<div class="stats-section">
    <h2><i class="fa fa-calendar-alt"></i> Statistiche per anno</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4" style="min-height: 200px;">
                <div class="card-header bg-primary text-white text-center py-2">
                    <h5 class="mb-0">Nascite per anno</h5>
                </div>
                <div class="card-body py-3">
                    <form action="" method="post">
                        <div class="row mb-3">
                            <label for="anno_persone" class="col-md-3 col-form-label text-md-end">Nell'anno:</label>
                            <div class="col-md-9">
                                <select name="anno_persone" id="anno_persone" class="form-select" style="width: 150px;">
                                    <?php
                                    $anno = 1940;
                                    if (isset($_POST['anno_persone'])) {
                                        $attuale = $_POST['anno_persone'];
                                        echo "<option value='$attuale'>$attuale</option>";
                                        
                                        $annata = $_POST['anno_persone'];
                                        $query = "SELECT * FROM `persone` WHERE year(DATA_NASCITA) = '$annata'";
                                        $result = $conn->query($query);
                                        if ($result) {
                                            $numero_persone_annata = $result->num_rows;
                                        }
                                    }
                                    
                                    while ($anno <= $anno_corrente) {
                                        echo "<option value='$anno'>$anno</option>";
                                        $anno++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-3 text-md-end">
                                <span>sono nate:</span>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" readonly value="<?php if (isset($numero_persone_annata)) echo $numero_persone_annata; else echo '0'; ?>">
                            </div>
                            <div class="col-md-3">
                                <span>persone</span>
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="submit" name="invio" class="btn btn-primary">Mostra</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Statistiche decessi -->
        <div class="col-md-6">
            <div class="card mb-4" style="min-height: 200px;">
                <div class="card-header bg-secondary text-white text-center py-2">
                    <h5 class="mb-0">Decessi per anno</h5>
                </div>
                <div class="card-body py-3">
                    <form name="form" id="form" action="" method="post">
                        <div class="row mb-3">
                            <label for="anno_persone2" class="col-md-3 col-form-label text-md-end">Nell'anno:</label>
                            <div class="col-md-9">
                                <select name="anno_persone2" id="anno_persone2" class="form-select" style="width: 150px;">
                                    <?php
                                    $anno2 = 1940;
                                    if (isset($_POST['anno_persone2'])) {
                                        $attuale2 = $_POST['anno_persone2'];
                                        echo "<option value='$attuale2'>$attuale2</option>";
                                        
                                        $annata2 = $_POST['anno_persone2'];
                                        $query = "SELECT * FROM `persone` WHERE year(DATA_MORTE) = '$annata2'";
                                        $result = $conn->query($query);
                                        if ($result) {
                                            $numero_persone_annata2 = $result->num_rows;
                                        }
                                    }
                                    
                                    while ($anno2 <= $anno_corrente) {
                                        echo "<option value='$anno2'>$anno2</option>";
                                        $anno2++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-3 text-md-end">
                                <span>sono decedute:</span>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" readonly value="<?php if (isset($numero_persone_annata2)) echo $numero_persone_annata2; else echo '0'; ?>">
                            </div>
                            <div class="col-md-3">
                                <span>persone</span>
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="submit" class="btn btn-secondary">Mostra</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
        
        <!-- Statistiche complessive -->
        <div class="stats-section">
            <h2><i class="fa fa-chart-line"></i> Statistiche complessive</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="summary-item text-center">
                        <h4>Abitanti per casa</h4>
                        <div class="summary-value"><?php echo (ceil($persone_casa * 10)) / 10; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-item text-center">
                        <h4>Età media della popolazione</h4>
                        <div class="summary-value"><?php echo $etamedia ?> anni</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-item text-center">
                        <h4>Persone decedute dall'inizio</h4>
                        <div class="summary-value"><?php echo $morti; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Dati demografici aggiuntivi -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Composizione demografica</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Categoria</th>
                                        <th>Numero</th>
                                        <th>Percentuale</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Maschi</td>
                                        <td><?php echo $numero_persone_m; ?></td>
                                        <td><?php echo round(($numero_persone_m / $numero_persone) * 100, 1); ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>Femmine</td>
                                        <td><?php echo $numero_persone_f; ?></td>
                                        <td><?php echo round(($numero_persone_f / $numero_persone) * 100, 1); ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>Minorenni</td>
                                        <td><?php echo $minorenni; ?></td>
                                        <td><?php echo round(($minorenni / $numero_persone) * 100, 1); ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>Maggiorenni</td>
                                        <td><?php echo $maggiorenni; ?></td>
                                        <td><?php echo round(($maggiorenni / $numero_persone) * 100, 1); ?>%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Distribuzione per fasce d'età</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fascia d'età</th>
                                        <th>Numero</th>
                                        <th>Percentuale</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>0-20 anni</td>
                                        <td><?php echo $minori20; ?></td>
                                        <td><?php echo round(($minori20 / $numero_persone) * 100, 1); ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>21-40 anni</td>
                                        <td><?php echo $persone20_40; ?></td>
                                        <td><?php echo round(($persone20_40 / $numero_persone) * 100, 1); ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>41-60 anni</td>
                                        <td><?php echo $persone_40_60; ?></td>
                                        <td><?php echo round(($persone_40_60 / $numero_persone) * 100, 1); ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>Oltre 60 anni</td>
                                        <td><?php echo $maggiori60; ?></td>
                                        <td><?php echo round(($maggiori60 / $numero_persone) * 100, 1); ?>%</td>
                                    </tr>
                                </tbody>
                            </table>
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
</body>
</html>