<?php
/*
*** vis_sto_tot_case.php
*** visualizzazione della storia delle case che hanno subìto modifiche nel corso del tempo
*** (i dati vengono presi dalla tabella storica delle case casa_sto)
*/
$config_path = __DIR__;
$util = $config_path . '/../util.php';
require $util;
setup();
isLogged("gestore");

// Recupero il numero totale di operazioni prima di tutto
$util2 = $config_path . '/../db/db_conn.php';
require_once $util2;
$query = "SELECT count(id) as cont FROM casa_sto";
$result = $conn->query($query);
$row = $result->fetch_array();
$all_rows = $row['cont'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storia delle Case</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }
        .table-container {
            overflow-x: auto;
        }
        .table {
            background-color: white;
        }
        .table th {
            background-color: #343a40;
            color: white;
            position: sticky;
            top: 0;
        }
        .form-select {
            max-width: 300px;
            display: inline-block;
        }
        .map-icon {
            color: #dc3545;
            font-size: 1.2rem;
        }
        .action-cell {
            white-space: nowrap;
        }
        h2 {
            color: #343a40;
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .operations-count {
            background-color: #e9ecef;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin-right: 15px;
        }
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .last-page-btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php stampaIntestazione(); ?>
        <?php stampaNavbar(); ?>

        <div class="filter-form">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="operations-count mb-2 mb-md-0">
                    Numero operazioni: <?php echo $all_rows; ?>
                </div>
                
                <form action="" method="post" class="d-flex align-items-center flex-wrap">
                    <div class="me-2 mb-2 mb-md-0">
                        <label for="tipo_operazione" class="me-2">Selezione del tipo di variazione:</label>
                    </div>
                    <div class="d-flex">
                        <select name="tipo_operazione" class="form-select me-2">
                            <option value="MOD">Modificate</option>
                            <option value="DELETE">Eliminate</option>
                            <option value="entrambe" selected>Tutte</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Filtra</button>
                    </div>
                </form>
            </div>
        </div>

        <h2 class="text-center">Storia delle variazioni delle case nel tempo</h2>

        <?php
        // Creo una variabile dove imposto il numero di record 
        // da mostrare in ogni pagina
        $x_pag = 10;

        // Recupero il numero di pagina corrente
        $pag = isset($_GET['pag']) ? $_GET['pag'] : 1;

        // Controllo se $pag è valorizzato e se è numerico
        if (!$pag || !is_numeric($pag)) $pag = 1;

        // definisco il numero totale di pagine
        $all_pages = ceil($all_rows / $x_pag);

        // Calcolo da quale record iniziare
        $first = ($pag - 1) * $x_pag;

        if (isset($_POST['tipo_operazione']))
            $tipo_operazione = $_POST['tipo_operazione'];

        $query =  "SELECT tipo_op, data_inizio_val, data_fine_val,";
        $query .= "id_casa, nome, id_moranca, nome_moranca,";
        $query .= "nome_capo_famiglia,nome_persona,  id_osm ";
        $query .= " FROM casa_sto ";

        if (isset($_POST['tipo_operazione'])) {
            $tipo_operazione = $_POST['tipo_operazione'];
            if($tipo_operazione!="entrambe") {
                $query .= "where tipo_op like '".$tipo_operazione."%' ";
            }
        }
        $query .= " ORDER BY id_casa DESC, data_fine_val DESC";
        $query .= " LIMIT $first, $x_pag";
        $result = $conn->query($query);

        if ($result->num_rows != 0) {
            echo '<div class="table-responsive table-container">';
            echo '<table class="table table-striped table-bordered table-hover">';
            echo '<thead class="thead-dark">';
            echo '<tr>';
            echo '<th scope="col">Tipo modifica</th>';
            echo '<th scope="col">ID Casa</th>';
            echo '<th scope="col">Nome</th>';
            echo '<th scope="col">Data inizio val.</th>';
            echo '<th scope="col">Data fine val.</th>';
            echo '<th scope="col">ID Moranca</th>';
            echo '<th scope="col">Nome Moranca</th>';
            echo '<th scope="col">Capo famiglia</th>';
            echo '<th scope="col">Nome persona</th>';
            echo '<th scope="col">Sulla mappa</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $result->fetch_array()) {
                echo '<tr>';
                echo '<td>' . $row['tipo_op'] . '</td>';
                echo '<td>' . $row['id_casa'] . '</td>';
                echo '<td>' . $row['nome'] . '</td>';
                echo '<td>' . $row['data_inizio_val'] . '</td>';
                echo '<td>' . $row['data_fine_val'] . '</td>';
                echo '<td>' . $row['id_moranca'] . '</td>';
                $mystr = utf8_encode($row['nome_moranca']);
                echo '<td>' . $mystr . '</td>';
                echo '<td>' . $row['nome_capo_famiglia'] . '</td>';
                echo '<td>' . $row['nome_persona'] . '</td>';

                $osm_link = "https://www.openstreetmap.org/way/$row[id_osm]";
                if ($row['id_osm'] != null && $row['id_osm'] != "0") { 
                    echo '<td class="action-cell">idOSM=' . $row['id_osm'] . ' <a href="' . $osm_link . '" target="_new"><i class="fas fa-map-marker-alt map-icon" title="Vai sulla mappa"></i></a></td>'; 	   
                } else { 
                    echo '<td>&nbsp;</td>';
                }  
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-info">Non vi sono variazioni sulle case.</div>';
        }

        // Paginazione
        echo '<div class="pagination-container">';
        echo '<div>';
        if ($pag > 1) {
            echo '<a href="?pag=1" class="btn btn-outline-primary">Prima pagina</a>';
            echo '<a href="?pag='.($pag-1).'" class="btn btn-outline-primary ms-2">Precedente</a>';
        }
        echo '</div>';
        
        echo '<div class="d-flex align-items-center mt-2 mt-md-0">';
        echo '<span class="me-3">Pagina '.$pag.' di '.$all_pages.'</span>';
        if ($pag < $all_pages) {
            echo '<a href="?pag='.($pag+1).'" class="btn btn-outline-primary me-2">Successiva</a>';
            echo '<a href="?pag='.$all_pages.'" class="btn btn-outline-primary last-page-btn">Ultima pagina</a>';
        }
        echo '</div>';
        echo '</div>';

        $result->free();
        $conn->close();

        echo '<div class="text-center mt-4">';
        echo '<a href="gest_case.php" class="btn btn-primary">';
        echo '<i class="fas fa-arrow-left me-2"></i>Torna a gestione case';
        echo '</a>';
        echo '</div>';
        ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>