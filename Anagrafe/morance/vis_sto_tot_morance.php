<?php
/*
*** preso da vis_moranca_sto.php
*** riadattata per renderlo unico a tutte le persone
*/
$config_path = __DIR__;
$util = $config_path . '/../util.php';
require $util;
setup();
isLogged("gestore");

// Funzione per formattare la data in gg-mm-yyyy
function formatDate($dateString) {
    if (empty($dateString)) return "";
    $date = new DateTime($dateString);
    return $date->format('d-m-Y');
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storia Moranças</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="../css/sto_morances.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<?php
$util2 = $config_path . '/../db/db_conn.php';
require_once $util2;
?>
<?php stampaIntestazione(); ?>

<body onload="myFunction()">
    <?php stampaNavbar(); ?>
    
    <div class="container mt-4">
        <?php
        // Creo una variabile dove imposto il numero di record 
        // da mostrare in ogni pagina
        $x_pag = 10;

        // Recupero il numero di pagina corrente.
        // Generalmente si utilizza una querystring
        $pag = isset($_GET['pag']) ? $_GET['pag'] : 1;

        // Controllo se $pag è valorizzato e se è numerico
        // ...in caso contrario gli assegno valore 1
        if (!$pag || !is_numeric($pag)) $pag = 1;

        $query = "SELECT count(id) as cont FROM morance_sto ";
        $result = $conn->query($query);
        $row = $result->fetch_array();
        $all_rows = $row['cont'];

        // definisco il numero totale di pagine
        $all_pages = ceil($all_rows / $x_pag);

        // Calcolo da quale record iniziare
        $first = ($pag - 1) * $x_pag;
        ?>

        <script>
            function myFunction() { //funzione per visualizzare un div (con una select dentro)quando si seleziona "modifica"
                var e = document.getElementById("tipo_operazione");
                var b = document.getElementById("div_invisibile");
                var selezionato = e.options[e.selectedIndex].text;
                if (selezionato == "Modifica")
                    b.style.visibility = "visible";
                else
                    b.style.visibility = "hidden";
            }
        </script>

        <div class="card mb-4">
            <div class="card-body">
                <form action="" method="post" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="tipo_operazione" class="col-form-label">Selezione del tipo di variazione:</label>
                    </div>
                    <div class="col-auto">
                        <select name="tipo_operazione" id="tipo_operazione" class="form-select">
                            <option value="mod">Modificate</option>
                            <option value="del">Eliminate</option>
                            <option value="entrambe" selected>Tutte</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filtra</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="h4">Storia delle variazioni delle moran&ccedil;as nel tempo</h2>
            </div>
            <div class="card-body">
                <?php
                $query =  "SELECT tipo_op, id_moranca, id_mor_zona, id_osm,";
                $query .= " nome as nome_moranca, cod_zona, data_inizio_val, data_fine_val ";
                $query .= " FROM morance_sto ";
                if (isset($_POST['tipo_operazione'])) {
                    $tipo_operazione = $_POST['tipo_operazione'];
                    if ($tipo_operazione != "entrambe") {
                        $query .= "where tipo_op like '" . $tipo_operazione . "%' ";
                    }
                }

                $query .= " ORDER BY id DESC, data_fine_val DESC";
                $query .= " LIMIT $first, $x_pag";

                $result = $conn->query($query);

                if ($result->num_rows != 0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped table-hover table-bordered">';
                    echo '<thead class="table-light">';
                    echo '<tr>';
                    echo '<th scope="col">Tipo modifica</th>';
                    echo '<th scope="col">ID moran&ccedil;a</th>';
                    echo '<th scope="col">Nome moran&ccedil;a</th>';
                    echo '<th scope="col">Zona</th>';
                    echo '<th scope="col">Sulla mappa</th>';
                    echo '<th scope="col">Data inizio val.</th>';
                    echo '<th scope="col">Data fine val.</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    while ($row = $result->fetch_array()) {
                        echo '<tr>';
                        echo '<td>' . $row['tipo_op'] . '</td>';
                        echo '<td>' . $row['id_moranca'] . '</td>';
                        echo '<td>' . utf8_encode($row['nome_moranca']) . '</td>';
                        echo '<td>' . $row['cod_zona'] . '</td>';
                        
                        // va sulla mappa OSM con id_OSM
                        $osm_link = "https://www.openstreetmap.org/way/$row[id_osm]";
                        if ($row['id_osm'] != null && $row['id_osm'] != "0") { 
                            echo '<td>idOSM=' . $row['id_osm'] . ' <a href="' . $osm_link . '" target="_blank" class="text-decoration-none"><i class="fas fa-map-marker-alt text-danger" title="Vai sulla mappa"></i></a></td>'; 	   
                        } else { 
                            echo '<td class="text-muted">N/D</td>';
                        }  
                        // Formattazione date in gg-mm-yyyy
                        echo '<td>' . formatDate($row['data_inizio_val']) . '</td>';
                        echo '<td>' . formatDate($row['data_fine_val']) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info">Non vi sono variazioni sulle moran&ccedil;as.</div>';
                }
                echo '<div class="mt-3">Numero operazioni: <span class="badge bg-primary">' . $all_rows . '</span></div>';
                
                // visualizza pagine
                $vis_pag = $config_path .'/../vis_pag.php';
                require $vis_pag;

                $result->free();
                $conn->close();
                ?>
            </div>
            <div class="card-footer">
                <a href="gest_morance.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Torna a gestione moran&ccedil;as
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>