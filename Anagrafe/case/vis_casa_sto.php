<?php
/*
*** vis_casa_sto.php
*** visualizzazione dati storici della casa
*** 14/3/2020: A.Carlone: correzioni varie
*** 28/02/2020: Ferraiuolo,Arneodo :aggiunta js
*/
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
isLogged("gestore");

// Connessione al database
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
if(!isset($conn)) {
    die("Connessione al database non riuscita");
}

// Funzione per formattare la data
function formatDate($dateString) {
    if(empty($dateString)) return '';
    $date = new DateTime($dateString);
    return $date->format('d-m-Y');
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storico Casa</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .table-container {
            overflow-x: auto;
        }
        .table {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #343a40;
            color: white;
            position: sticky;
            top: 0;
        }
        h2 {
            color: #343a40;
            margin-top: 30px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        .osm-icon {
            width: 20px;
            height: 20px;
            margin-left: 5px;
        }
        .back-link {
            margin-top: 20px;
            display: inline-block;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }
         h2 {
            color: black;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body onload="myFunction()">
    <div class="container-fluid">
        <?php stampaIntestazione(); ?>
        <?php stampaNavbar(); ?>
        
        <?php
        if(!isset($_POST['id_casa'])) {
            $id_casa=$_SESSION['pag_c']['sto'];
        } else {
            $id_casa=$_POST['id_casa'];
            $_SESSION['pag_c']['sto']=$id_casa;
        }

        // Paginazione
        $x_pag = 10;
        $pag = isset($_GET['pag']) ? $_GET['pag'] : 1;
        if (!$pag || !is_numeric($pag)) $pag = 1; 

        $query = "SELECT count(id) as cont FROM casa_sto where id_casa=$id_casa";
        $result = $conn->query($query);
        if(!$result) {
            die("Errore nella query: " . $conn->error);
        }
        $row = $result->fetch_array();
        $all_rows= $row['cont'];
        $all_pages = ceil($all_rows / $x_pag);
        $first = ($pag - 1) * $x_pag;
        ?>

        <script>
        function myFunction() {
            var e = document.getElementById("tipo_operazione");
            var b=document.getElementById("div_invisibile");
            var selezionato = e.options[e.selectedIndex].text;
            if(selezionato=="Modifica") {
                b.style.visibility="visible";
            } else {
                b.style.visibility="hidden"; 
            }
        }
        </script>
        <h2>Storico delle case: </h2>
        <div class="card">
            <div class="card-header">
                Situazione attuale della casa
            </div>
            <div class="card-body">
                <?php
                // visualizzazione situazione attuale
                $query =  "SELECT p.id as id_pers, p.nominativo as capo_famiglia ";
                $query .= " FROM persone p INNER JOIN pers_casa pc ON p.id = pc.id_pers ";
                $query .= " INNER JOIN casa c ON c.id = pc.id_casa ";   
                $query .= " WHERE c.id = $id_casa";
                $query .=" AND pc.cod_ruolo_pers_fam = 'CF'";

                $result = $conn->query($query);
                if (!$result) {
                    die("Errore nella query: " . $conn->error);
                }
                $row = $result->fetch_array();
                if ($result->num_rows >0) {
                    $capo_famiglia = "";
                } else {
                    $capo_famiglia = $row['capo_famiglia'];
                }

                $query =  "SELECT DISTINCT";
                $query .= " c.id as id_casa, c.nome as nome_casa, c.id_moranca, c.id_osm, c.data_inizio_val,";
                $query .= " m.nome as nome_moranca, p.nominativo as capo_famiglia";
                $query .= " FROM ";
                $query .= " casa c, pers_casa pc, morance m, persone p ";   
                $query .= " WHERE c.id = $id_casa";
                $query .= " AND m.id = c.id_moranca";
                $query .= " AND c.id = pc.id_casa";
                $query .= " AND p.id = pc.id_pers";
                $query .= " AND pc.cod_ruolo_pers_fam = 'CF'";

                $result = $conn->query($query);
                if(!$result) {
                    die("Errore nella query: " . $conn->error);
                }
                
                if ($result->num_rows ==1) {
                    while ($row = $result->fetch_array()) {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-bordered table-striped">';
                        echo '<thead class="thead-dark">';
                        echo '<tr>';
                        echo '<th>ID Casa</th>';
                        echo '<th>Data Inizio Val.</th>';
                        echo '<th>Nome</th>';
                        echo '<th>ID Moranca</th>';
                        echo '<th>Moranca</th>';
                        echo '<th>Capo Famiglia</th>';
                        echo '<th>ID OSM</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        echo '<tr>';
                        echo '<td>'.$row['id_casa'].'</td>';
                        echo '<td>'.formatDate($row['data_inizio_val']).'</td>';
                        echo '<td>'.$row['nome_casa'].'</td>';
                        echo '<td>'.$row['id_moranca'].'</td>';
                        echo '<td>'.utf8_encode($row['nome_moranca']).'</td>';
                        echo '<td>'.utf8_encode($row['capo_famiglia']).'</td>';
                        echo '<td>'.$row['id_osm'].'</td>';
                        echo '</tr>';
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Storia della casa
            </div>
            <div class="card-body">
                <?php
                if (isset($_POST['tipo_operazione'])) $tipo_operazione = $_POST['tipo_operazione'];
                if (isset($_POST['valore_operazione'])) $valore_operazione = $_POST['valore_operazione'];
                      
                $query =  "SELECT tipo_op, data_inizio_val, data_fine_val,";
                $query .= "id_casa, nome, id_moranca, nome_moranca,";
                $query .= "nome_capo_famiglia, id_osm ";
                $query .= " FROM casa_sto ";
                $query .= " WHERE id_casa=$id_casa";
                $query .= " ORDER BY id_casa DESC, data_fine_val DESC";
                $query .= " LIMIT $first, $x_pag";
                
                $result = $conn->query($query);
                if(!$result) {
                    die("Errore nella query: " . $conn->error);
                }
                
                if ($result->num_rows !=0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered table-striped">';
                    echo '<thead class="thead-dark">';
                    echo '<tr>';
                    echo '<th>Tipo Modifica</th>';
                    echo '<th>ID Casa</th>';
                    echo '<th>Nome</th>';
                    echo '<th>Data Inizio Val.</th>';
                    echo '<th>Data Fine Val.</th>';
                    echo '<th>ID Moranca</th>';
                    echo '<th>Moranca</th>';
                    echo '<th>Capo Famiglia</th>';
                    echo '<th>ID OSM</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    while ($row = $result->fetch_array()) {
                        echo '<tr>';
                        echo '<td>'.$row['tipo_op'].'</td>';
                        echo '<td>'.$row['id_casa'].'</td>';
                        echo '<td>'.$row['nome'].'</td>';
                        echo '<td>'.formatDate($row['data_inizio_val']).'</td>';
                        echo '<td>'.formatDate($row['data_fine_val']).'</td>';
                        echo '<td>'.$row['id_moranca'].'</td>';
                        $mystr = utf8_encode($row['nome_moranca']);
                        echo '<td>'.$mystr.'</td>';
                        echo '<td>'.$row['nome_capo_famiglia'].'</td>';
                                
                        $osm_link = "https://www.openstreetmap.org/way/".$row['id_osm'];
                        if ($row['id_osm'] != null) { 
                            echo '<td>'.$row['id_osm'].'&nbsp;<a href="'.$osm_link.'" target="_blank">vai alla mappa <i class="fas fa-external-link-alt"></i></a></td>'; 
                        } else { 
                            echo '<td>'.$row['id_osm'].'&nbsp;</td>';
                        }
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info">Non vi sono variazioni sulla casa.</div>';
                }
                echo '<p class="text-center">Numero operazioni: '.$all_rows.'</p>';

                // visualizza pagine
                $vis_pag = $config_path .'/../vis_pag.php';
                require $vis_pag;

                $result->free();
                $conn->close();	
                ?>
                <a href="gest_case.php" class="btn btn-primary back-link">Torna a gestione case</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>