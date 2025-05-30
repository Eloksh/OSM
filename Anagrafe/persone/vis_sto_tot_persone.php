<?php
/*
*** vis_sto_tot_persone.php
*** visualizzazione storico generale delle persone
*** visualizza la variazione delle persone nel tempo (tabella persone_sto)
*/
$config_path = __DIR__;
$util = $config_path . '/../util.php';
require $util;
setup();
isLogged("gestore");

// Funzione per convertire le date da YYYY-MM-DD a DD/MM/YYYY
function formatDate($date) {
    if(empty($date) || $date == '0000-00-00') return '';
    $dateParts = explode('-', $date);
    if(count($dateParts) == 3) {
        return $dateParts[2].'/'.$dateParts[1].'/'.$dateParts[0];
    }
    return $date;
}

// Connessione al database
$util2 = $config_path . '/../db/db_conn.php';
require_once $util2;

// Configurazione paginazione
$x_pag = 10;
$pag = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
if (!$pag || !is_numeric($pag)) $pag = 1;

// Conteggio record totali
$count_query = "SELECT count(id) as cont FROM persone_sto";
$count_result = $conn->query($count_query);
$row = $count_result->fetch_array();
$all_rows = $row['cont'];
$all_pages = ceil($all_rows / $x_pag);
$first = ($pag - 1) * $x_pag;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storico Variazioni Persone</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --table-header-bg: #343a40;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .filter-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .page-title {
            color: var(--secondary-color);
            margin-bottom: 25px;
            font-weight: 600;
            text-align: center;
        }
        
        .data-table {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table thead th {
            background-color: var(--table-header-bg);
            color: white;
            position: sticky;
            top: 0;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .pagination-info {
            background-color: #e9ecef;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 20px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 15px;
            }
            
            .filter-card {
                padding: 15px;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <?php stampaIntestazione(); ?>
        <?php stampaNavbar(); ?>
        
        <div class="filter-card">
            <form action="" method="post" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="tipo_operazione" class="form-label">Selezione del tipo di variazione:</label>
                </div>
                <div class="col-md-5">
                    <select name="tipo_operazione" id="tipo_operazione" class="form-select">
                        <option value="modificato">Modificate</option>
                        <option value="eliminato">Eliminate</option>
                        <option value="entrambe" selected>Tutte</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filtra</button>
                </div>
            </form>
        </div>
        
        <h2 class="page-title">Storia delle variazioni delle persone nel tempo</h2>
        
        <?php
        // Costruzione query
        $query = "SELECT tipo_op, cod_ruolo_pers_fam, id_persona, nominativo, sesso, 
                 matricola_stud, data_nascita, data_morte, id_casa, nome_casa, 
                 cod_ruolo_pers_fam, desc_ruolo_pers_fam, data_inizio_val, data_fine_val 
                 FROM persone_sto ";
        
        if (isset($_POST['tipo_operazione'])) {
            $tipo_operazione = $_POST['tipo_operazione'];
            if($tipo_operazione != "entrambe") {
                $query .= "WHERE tipo_op LIKE '".$tipo_operazione."%' ";
            }
        }
        
        $query .= "ORDER BY id DESC, data_fine_val DESC LIMIT $first, $x_pag";
        $result = $conn->query($query);
        
        if ($result->num_rows != 0): ?>
            <div class="data-table">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Operazione</th>
                                <th>Data Inizio</th>
                                <th>Data Fine</th>
                                <th>ID Persona</th>
                                <th>Nominativo</th>
                                <th>Sesso</th>
                                <th>Nascita</th>
                                <th>Morte</th>
                                <th>ID Casa</th>
                                <th>Casa</th>
                                <th>Cod. Ruolo</th>
                                <th>Ruolo</th>
                                <th>Matricola</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_array()): ?>
                                <tr>
                                    <td><?= $row['tipo_op'] ?></td>
                                    <td><?= formatDate($row['data_inizio_val']) ?></td>
                                    <td><?= formatDate($row['data_fine_val']) ?></td>
                                    <td><?= $row['id_persona'] ?></td>
                                    <td><?= utf8_encode($row['nominativo']) ?></td>
                                    <td><?= $row['sesso'] ?></td>
                                    <td><?= formatDate($row['data_nascita']) ?></td>
                                    <td><?= formatDate($row['data_morte']) ?></td>
                                    <td><?= $row['id_casa'] ?></td>
                                    <td><?= $row['nome_casa'] ?></td>
                                    <td><?= $row['cod_ruolo_pers_fam'] ?></td>
                                    <td><?= $row['desc_ruolo_pers_fam'] ?></td>
                                    <td><?= $row['matricola_stud'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Non vi sono variazioni sulle persone.</div>
        <?php endif; ?>
        
        <!-- Paginazione -->
        <div class="pagination-container">
            <div class="pagination-info">
                Numero operazioni: <strong><?= $all_rows ?></strong>
            </div>
            
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($pag > 1): ?>
                        <li class="page-item"><a class="page-link" href="?pag=1">Prima</a></li>
                        <li class="page-item"><a class="page-link" href="?pag=<?= $pag-1 ?>">Precedente</a></li>
                    <?php endif; ?>
                    
                    <?php 
                    $start_page = max(1, $pag - 2);
                    $end_page = min($all_pages, $pag + 2);
                    
                    if ($start_page > 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    
                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= $i == $pag ? 'active' : '' ?>">
                            <a class="page-link" href="?pag=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor;
                    
                    if ($end_page < $all_pages) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    
                    if ($pag < $all_pages): ?>
                        <li class="page-item"><a class="page-link" href="?pag=<?= $pag+1 ?>">Successiva</a></li>
                        <li class="page-item"><a class="page-link" href="?pag=<?= $all_pages ?>">Ultima</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        
        <a href="gest_persone.php" class="btn btn-primary back-link">
            <i class="fas fa-arrow-left me-2"></i>Torna a gestione persone
        </a>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>