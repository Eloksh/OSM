<?php
/*
*** vis_persona_sto: visualizzazione dati storici di una persona
*** 14/3/2020: A.Carlone: correzioni varie
*** 02/03/20  Gobbi Dennis 
*/
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
isLogged("gestore");

// Funzione per formattare le date
function formatDate($dateString) {
    if (empty($dateString)) return '';
    return date('d-m-Y', strtotime($dateString));
}

// Correzione per l'errore "Undefined array key 'id_persona'"
$id_persona = isset($_REQUEST['id_persona']) ? $_REQUEST['id_persona'] : (isset($_POST['id_persona']) ? $_POST['id_persona'] : null);
if (!$id_persona || !is_numeric($id_persona)) {
    die("ID persona non valido o mancante");
}

$x_pag = 10; // Record per pagina
$pag = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
if ($pag < 1) $pag = 1;

$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <style>
        .container-main {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-top: 30px;
            margin-bottom: 20px;
            text-align: center;
        }
        .table-responsive {
            margin: 20px 0;
            overflow-x: auto;
        }
        .table th {
            background-color: #0d6efd;
            color: white;
            position: sticky;
            top: 0;
            text-align: center;
            vertical-align: middle;
        }
        .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table-hover tbody tr:hover {
            background-color: #e9f7ff;
        }
        .pagination-container {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #0d6efd;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .filter-section {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #e9f7ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .table-sm th, .table-sm td {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .alert-warning, .alert-info {
            text-align: center;
        }
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.8rem;
            }
            .table th, .table td {
                padding: 0.3rem;
            }
        }
    </style>
</head>
<body onload="myFunction()">
    <?php stampaIntestazione(); ?>
    <?php stampaNavbar(); ?>

    <div class="container container-main">
        <?php
        // Conta record totali
        $count_query = "SELECT count(id) as cont FROM persone_sto where id_persona='$id_persona'";
        $count_result = $conn->query($count_query);
        if (!$count_result) {
            echo "<div class='alert alert-danger'>Errore nel database: " . $conn->error . "</div>";
        } else {
            $count_row = $count_result->fetch_array();
            $all_rows = $count_row['cont'];
            $all_pages = ceil($all_rows / $x_pag);
            $first = ($pag - 1) * $x_pag;
        }
        ?>

        <h2 class="section-title">Situazione attuale della persona</h2>
        
        <?php
        // Visualizzazione situazione attuale
        $query = "SELECT p.id, p.nominativo, sesso, matricola_stud, data_nascita, data_morte,
                  c.id as id_casa, c.nome as nome_casa, pc.cod_ruolo_pers_fam, 
                  rpf.descrizione as desc_ruolo_pers_fam, s.matricola
                  FROM persone p 
                  LEFT JOIN studenti s ON s.matricola = p.matricola_stud
                  INNER JOIN pers_casa pc ON p.id = pc.id_pers
                  INNER JOIN casa c ON c.id = pc.id_casa
                  INNER JOIN ruolo_pers_fam rpf ON rpf.cod = pc.cod_ruolo_pers_fam
                  WHERE p.id = $id_persona";
        
        $result = $conn->query($query);
        
        // Correzione per l'errore di sintassi SQL
        if (!$result) {
            echo "<div class='alert alert-danger'>Errore nella query: " . $conn->error . "</div>";
        } elseif ($result->num_rows == 1) {
            $row = $result->fetch_array();
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nominativo</th>
                            <th>Sesso</th>
                            <th>Nascita</th>
                            <th>Morte</th>
                            <th>Casa ID</th>
                            <th>Casa</th>
                            <th>Ruolo Cod</th>
                            <th>Ruolo Desc</th>
                            <th>Matricola</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo utf8_encode($row['nominativo']); ?></td>
                            <td><?php echo $row['sesso']; ?></td>
                            <td><?php echo formatDate($row['data_nascita']); ?></td>
                            <td><?php echo formatDate($row['data_morte']); ?></td>
                            <td><?php echo $row['id_casa']; ?></td>
                            <td><?php echo $row['nome_casa']; ?></td>
                            <td><?php echo $row['cod_ruolo_pers_fam']; ?></td>
                            <td><?php echo $row['desc_ruolo_pers_fam']; ?></td>
                            <td><?php echo $row['matricola']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning">Nessun record trovato per la situazione attuale</div>
        <?php } ?>

        <h2 class="section-title">Storia della persona</h2>
        
        <?php if (isset($all_rows)): ?>
            <div class="info-box">
                Numero totale di operazioni: <strong><?php echo $all_rows; ?></strong>
            </div>
        <?php endif; ?>

        <?php
        // Visualizzazione situazione storica
        $query = "SELECT tipo_op, cod_ruolo_pers_fam, id_persona, nominativo, sesso, 
                  matricola_stud, data_nascita, data_morte, id_casa, nome_casa, 
                  cod_ruolo_pers_fam, desc_ruolo_pers_fam, data_inizio_val, data_fine_val
                  FROM persone_sto 
                  WHERE id_persona = $id_persona
                  ORDER BY id DESC, data_fine_val DESC
                  LIMIT $first, $x_pag";
        
        $result = $conn->query($query);
        
        if (!$result) {
            echo "<div class='alert alert-danger'>Errore nella query storica: " . $conn->error . "</div>";
        } elseif ($result->num_rows > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Operazione</th>
                            <th>Inizio Validità</th>
                            <th>Fine Validità</th>
                            <th>ID Persona</th>
                            <th>Nominativo</th>
                            <th>Sesso</th>
                            <th>Nascita</th>
                            <th>Morte</th>
                            <th>Casa ID</th>
                            <th>Casa</th>
                            <th>Ruolo Cod</th>
                            <th>Ruolo Desc</th>
                            <th>Matricola</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_array()): ?>
                            <tr>
                                <td><?php echo $row['tipo_op']; ?></td>
                                <td><?php echo formatDate($row['data_inizio_val']); ?></td>
                                <td><?php echo formatDate($row['data_fine_val']); ?></td>
                                <td><?php echo $row['id_persona']; ?></td>
                                <td><?php echo utf8_encode($row['nominativo']); ?></td>
                                <td><?php echo $row['sesso']; ?></td>
                                <td><?php echo formatDate($row['data_nascita']); ?></td>
                                <td><?php echo formatDate($row['data_morte']); ?></td>
                                <td><?php echo $row['id_casa']; ?></td>
                                <td><?php echo $row['nome_casa']; ?></td>
                                <td><?php echo $row['cod_ruolo_pers_fam']; ?></td>
                                <td><?php echo $row['desc_ruolo_pers_fam']; ?></td>
                                <td><?php echo $row['matricola_stud']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-info">Non vi sono variazioni storiche per questa persona</div>
        <?php } ?>

        <!-- Pagination -->
        <?php if (isset($all_pages) && $all_pages > 1): ?>
            <div class="pagination-container">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($pag > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pag=<?php echo $pag-1; ?>&id_persona=<?php echo $id_persona; ?>">
                                    <i class="fa fa-chevron-left"></i> Precedente
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i=1; $i<=$all_pages; $i++): ?>
                            <li class="page-item <?php echo ($i==$pag) ? 'active' : ''; ?>">
                                <a class="page-link" href="?pag=<?php echo $i; ?>&id_persona=<?php echo $id_persona; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pag < $all_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pag=<?php echo $pag+1; ?>&id_persona=<?php echo $id_persona; ?>">
                                    Successiva <i class="fa fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <a href="gest_persone.php" class="btn btn-primary back-link">
                <i class="fa fa-arrow-left"></i> Torna a gestione persone
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function myFunction() {
            var e = document.getElementById("tipo_operazione");
            var b = document.getElementById("div_invisibile");
            if (e) {
                var selezionato = e.options[e.selectedIndex].text;
                if (selezionato == "Modifica" && b) {
                    b.style.visibility = "visible";
                } else if (b) {
                    b.style.visibility = "hidden"; 
                }
            }
        }
    </script>
</body>
</html>