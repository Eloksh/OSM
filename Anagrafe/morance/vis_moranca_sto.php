<?php
/*
*** vis_moranca_sto.php
*** visualizzazione dati storici della moranca
*** 14/3/2020: A.Carlone: correzioni varie
*/
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
isLogged("gestore");

// Function to format date from YYYY-MM-DD to DD-MM-YYYY
function formatDate($dateString) {
    if(empty($dateString)) return '';
    $date = DateTime::createFromFormat('Y-m-d', $dateString);
    return $date ? $date->format('d-m-Y') : $dateString;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storico Morança</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
            --border-radius: 0.375rem;
            --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: #212529;
            line-height: 1.6;
        }
        
        .page-title {
            color: var(--secondary-color);
            font-weight: 600;
            margin-top: -50px;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 1.5rem rgba(0, 0, 0, 0.2);
        }
        
        .card-header {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 600;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 500;
            padding: 0.75rem;
            text-align: left;
        }
        
        td {
            padding: 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }
        
        tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-outline-secondary {
            color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .map-icon {
            color: var(--accent-color);
            font-size: 1.25rem;
            transition: var(--transition);
        }
        
        .map-icon:hover {
            transform: scale(1.2);
        }
        
        .pagination {
            justify-content: center;
            margin-top: 1.5rem;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .page-link {
            color: var(--secondary-color);
        }
        
        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.5rem 1rem;
            background-color: var(--secondary-color);
            color: white;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .back-link:hover {
            background-color: #1a252f;
            color: white;
            transform: translateY(-2px);
        }
        
        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .card {
                border-radius: 0;
            }
            
            th, td {
                padding: 0.5rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <?php stampaIntestazione(); ?>
    <?php stampaNavbar(); ?>
    
    <div class="container py-4">
        <h1 class="page-title">Storico Morança</h1>
        
        <?php
        $util2 = $config_path .'/../db/db_conn.php';
        require_once $util2;
        
        $id_moranca = $_POST['id_moranca'];
        $x_pag = 10;
        $pag = isset($_GET['pag']) ? $_GET['pag'] : 1;
        
        if (!$pag || !is_numeric($pag)) $pag = 1; 
        
        $query = "SELECT count(id) as cont FROM morance_sto ";
        $result = $conn->query($query);
        $row = $result->fetch_array();
        $all_rows = $row['cont'];
        $all_pages = ceil($all_rows / $x_pag);
        $first = ($pag - 1) * $x_pag;
        ?>
        
        <script>
        function myFunction() {
            var e = document.getElementById("tipo_operazione");
            var b = document.getElementById("div_invisibile");
            var selezionato = e.options[e.selectedIndex].text;
            if(selezionato == "Modifica") {
                b.style.visibility = "visible";
            } else {
                b.style.visibility = "hidden"; 
            }
        }
        </script>
        
        <!-- Current Situation Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Situazione attuale della morança</h3>
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT ";
                $query .= " m.id as id_moranca, m.nome as nome_moranca, z.nome as zona, m.id_mor_zona as id_mor_zona,";
                $query .= " m.id_osm as id_osm,";
                $query .= " m.data_inizio_val as data_inizio_val, m.data_fine_val as data_fine_val";
                $query .= " FROM morance m, zone z ";
                $query .= " WHERE m.cod_zona = z.cod ";
                $query .= " AND m.id = $id_moranca";
                
                $result = $conn->query($query);
                
                if ($result->num_rows == 1) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-hover">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>ID Morança</th>';
                    echo '<th>ID Morança-Zona</th>';
                    echo '<th>Nome</th>';
                    echo '<th>Zona</th>';
                    echo '<th>Sulla Mappa</th>';
                    echo '<th>Data Inizio Val.</th>';
                    echo '<th>Data Fine Val.</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    while ($row = $result->fetch_array()) {
                        echo '<tr>';
                        echo '<td>'.$row['id_moranca'].'</td>';
                        echo '<td>'.$row['id_mor_zona'].'</td>';
                        echo '<td>'.utf8_encode($row['nome_moranca']).'</td>';
                        echo '<td>'.$row['zona'].'</td>';
                        
                        $osm_link = "https://www.openstreetmap.org/way/$row[id_osm]";
                        if ($row['id_osm'] != null && $row['id_osm'] != "0") {
                            echo '<td>idOSM='.$row['id_osm'].' <a href="'.$osm_link.'" target="_new" class="map-icon"><i class="fas fa-map-marker-alt" title="Vai sulla mappa"></i></a></td>';
                        } else {
                            echo '<td class="text-muted">N/A</td>';
                        }
                        
                        echo '<td>'.formatDate($row['data_inizio_val']).'</td>';
                        echo '<td>'.formatDate($row['data_fine_val']).'</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <!-- History Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Storia della morança</h3>
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT tipo_op, id_moranca, id_mor_zona, id_osm, ";
                $query .= "nome as nome_moranca, cod_zona, data_inizio_val, data_fine_val ";
                $query .= "FROM morance_sto ";
                $query .= "WHERE id_moranca = $id_moranca ";
                $query .= "ORDER BY id DESC, data_fine_val DESC ";
                $query .= "LIMIT $first, $x_pag";
                
                $result = $conn->query($query);
                
                if ($result->num_rows != 0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-hover">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Tipo Modifica</th>';
                    echo '<th>ID Morança</th>';
                    echo '<th>ID Morança-Zona</th>';
                    echo '<th>Nome Morança</th>';
                    echo '<th>Zona</th>';
                    echo '<th>Sulla Mappa</th>';
                    echo '<th>Data Inizio Val.</th>';
                    echo '<th>Data Fine Val.</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    while ($row = $result->fetch_array()) {
                        echo '<tr>';
                        echo '<td>'.$row['tipo_op'].'</td>';
                        echo '<td>'.$row['id_moranca'].'</td>';
                        echo '<td>'.$row['id_mor_zona'].'</td>';
                        echo '<td>'.utf8_encode($row['nome_moranca']).'</td>';
                        echo '<td>'.$row['cod_zona'].'</td>';
                        
                        $osm_link = "https://www.openstreetmap.org/way/$row[id_osm]";
                        if ($row['id_osm'] != null && $row['id_osm'] != "0") {
                            echo '<td>idOSM='.$row['id_osm'].' <a href="'.$osm_link.'" target="_new" class="map-icon"><i class="fas fa-map-marker-alt" title="Vai sulla mappa"></i></a></td>';
                        } else {
                            echo '<td class="text-muted">N/A</td>';
                        }
                        
                        echo '<td>'.formatDate($row['data_inizio_val']).'</td>';
                        echo '<td>'.formatDate($row['data_fine_val']).'</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                    
                    echo '<div class="d-flex justify-content-between align-items-center mt-3">';
                    echo '<div class="text-muted">Numero operazioni: '.$all_rows.'</div>';
                    
                    // Pagination
                    $vis_pag = $config_path .'/../vis_pag.php';
                    require $vis_pag;
                    
                    echo '</div>';
                } else {
                    echo '<div class="no-data">';
                    echo 'Non vi sono state variazioni per la morança.';
                    echo '</div>';
                }
                
                $result->free();
                $conn->close();
                ?>
            </div>
        </div>
        
        <a href="gest_morance.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Torna a gestione moranças
        </a>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>