<?php
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
isLogged("gestore");

// Handle undefined pag_m key with null coalescing operator
$pag = $_SESSION['pag_m']['pag_m'] ?? 1; // Default to page 1 if not set
if (isset($_SESSION['pag_m'])) {
    unset($_SESSION['pag_m']);
}

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
    <title>Elenco Case - Morança</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            padding: 0;
            margin: 0;
        }
        .page-header {
            color: var(--secondary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
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
        
        .btn-action {
            padding: 0.375rem 0.5rem;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .btn-edit {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
        }
        
        .btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-delete {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--accent-color);
        }
        
        .btn-delete:hover {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-people {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }
        
        .btn-people:hover {
            background-color: #2ecc71;
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
        <?php
        $util2 = $config_path .'/../db/db_conn.php';
        require_once $util2;
        
        $id_moranca = $_POST["id_moranca"] ?? null;
        
        if (!$id_moranca) {
            die("ID morança non specificato");
        }
        
        $query = "SELECT nome FROM morance WHERE id = ". $id_moranca;
        $result = $conn->query($query);
        $row = $result->fetch_array();
        $nome_moranca = utf8_encode($row['nome']);
        $result->free();
        ?>
        
        <div class="page-header">
            <h1>Villaggio di Nague</h1>
        </div>
        
        <div class="card">
            <h2 {
            color: var(--secondary-color);
        }}>Elenco case della morança: <?php echo htmlspecialchars($nome_moranca); ?> (ID=<?php echo htmlspecialchars($id_moranca); ?>)</h2 {
            color: var(--secondary-color);
        }}>
            <div class="card-header">
                <h3 class="mb-0">Dettagli Case</h3>
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT c.id, c.nome,";
                $query .= " z.nome zona, c.id_moranca, m.nome nome_moranca, c.nome, p.id id_pers, p.nominativo, m.id_osm, ";
                $query .= " m.data_inizio_val, m.data_fine_val";
                $query .= " FROM morance m INNER JOIN casa c ON m.id = c.id_moranca ";
                $query .= " INNER JOIN zone z ON z.cod = m.cod_zona ";
                $query .= " LEFT JOIN pers_casa pc ON c.id = pc.id_casa ";
                $query .= " AND pc.cod_ruolo_pers_fam = 'CF'";
                $query .= " LEFT JOIN persone p ON p.id = pc.id_pers";
                $query .= " WHERE m.id = $id_moranca ";
                $query .= " ORDER BY c.id ASC";
                $result = $conn->query($query);
                
                if ($result->num_rows != 0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-hover">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Nome</th>';
                    echo '<th>Zona</th>';
                    echo '<th>ID Morança</th>';
                    echo '<th>Morança</th>';
                    echo '<th>ID Capo Fam.</th>';
                    echo '<th>Capo Famiglia</th>';
                    echo '<th>Abitanti</th>';
                    echo '<th>ID OSM</th>';
                    echo '<th>Data Inizio</th>';
                    echo '<th>Data Fine</th>';
                    echo '<th>Azioni</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    while ($row = $result->fetch_array()) {
                        echo '<tr>';
                        echo '<td>'.htmlspecialchars($row['id']).'</td>';
                        echo '<td>'.htmlspecialchars($row['nome']).'</td>';
                        echo '<td>'.htmlspecialchars($row['zona']).'</td>';
                        echo '<td>'.htmlspecialchars($row['id_moranca']).'</td>';
                        echo '<td>'.htmlspecialchars(utf8_encode($row['nome_moranca'])).'</td>';
                        echo '<td>'.htmlspecialchars($row['id_pers']).'</td>';
                        echo '<td>'.htmlspecialchars(utf8_encode($row['nominativo'])).'</td>';
                        
                        $query2 = "SELECT COUNT(pers_casa.ID_PERS) as persone from pers_casa WHERE ID_CASA='".$row['id']."'";
                        $result2 = $conn->query($query2);
                        $row2 = $result2->fetch_array();
                        echo '<td>'.htmlspecialchars($row2['persone']).'</td>';
                        
                        $osm_link = "https://www.openstreetmap.org/way/".$row['id_osm'];
                        if (($row['id_osm'] != null) && ($row['id_osm'] != 0)) {
                            echo '<td>'.htmlspecialchars($row['id_osm']).' <a href="'.$osm_link.'" target="_new" class="map-icon"><i class="fas fa-map-marker-alt"></i></a></td>';
                        } else {
                            echo '<td class="text-muted">N/A</td>';
                        }
                        
                        echo '<td>'.formatDate($row['data_inizio_val']).'</td>';
                        echo '<td>'.formatDate($row['data_fine_val']).'</td>';
                        
                        echo '<td class="d-flex gap-2">';
                        echo '<form method="post" action="../case/mod_casa.php">';
                        echo '<button class="btn-action btn-edit" name="id_casa" value="'.$row['id'].'" type="submit" title="Modifica">';
                        echo '<i class="fas fa-wrench"></i>';
                        echo '</button>';
                        echo '</form>';
                        
                        echo '<form method="post" action="../case/del_casa.php">';
                        echo '<button class="btn-action btn-delete" name="id_casa" value="'.$row['id'].'" type="submit" title="Elimina">';
                        echo '<i class="fas fa-trash"></i>';
                        echo '</button>';
                        echo '</form>';
                        
                        echo '<form method="post" action="../case/mostra_persone.php">';
                        echo '<button class="btn-action btn-people" name="id_casa" value="'.$row['id'].'" type="submit" title="Persone">';
                        echo '<i class="fas fa-users"></i>';
                        echo '</button>';
                        echo '</form>';
                        echo '</td>';
                        
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<div class="no-data">';
                    echo 'Nessuna casa è presente.';
                    echo '</div>';
                }
                
                $result->free();
                $conn->close();
                ?>
            </div>
        </div>
        
        <a href="gest_morance.php?pag=<?php echo htmlspecialchars($pag); ?>" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Torna a gestione moranças
        </a>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>