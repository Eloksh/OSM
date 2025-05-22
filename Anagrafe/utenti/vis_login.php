<?php
/*
*** Autore: Ferraiuolo
*** Descrizione: vis_login.php
*** visualizzazione accessi al sistema
*/

// Prima di qualsiasi output, inizializzare la sessione
// Risolve: "session_start(): Session cannot be started after headers have already been sent"
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
isLogged("amministratore");

// Connessione al database
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizzazione Accessi al Sistema</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/login_logs.css">
</head>
<body>
    <?php 
    stampaIntestazione(); 
    stampaNavbar(); 
    ?>

    <div class="login-header text-center">
        <h2><i class="fas fa-user-shield"></i> Accessi al Sistema</h2>
        <p class="mt-2" style="color: black;">Monitoraggio degli accessi effettuati</p>
    </div>
    
    <div class="container">
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

        $filtro = ""; //filtro vuoto

        if (isset($_SESSION['filtro_login_logs']) && !isset($_POST['filtro'])) //se è salvato in sessione
            $filtro = $_SESSION['filtro_login_logs'];

        if (isset($_POST['filtro'])) //se è stato applicato un filtro
        {
            if($_POST['filtro'] == "disattivato") {
                $filtro = "";
                $_SESSION['filtro_login_logs'] = "";
            }
            if($_POST['filtro'] == "riusciti") {
                $filtro = " WHERE USER IS NOT NULL";
                $_SESSION['filtro_login_logs'] = " WHERE USER IS NOT NULL";
            }
            if($_POST['filtro'] == "falliti") {
                $filtro = " WHERE USER IS NULL";
                $_SESSION['filtro_login_logs'] = " WHERE USER IS NULL";
            }
        }  

        $query = "SELECT count(*) as cont FROM login_logs";
        $query .= $filtro;
        $result = $conn->query($query);
        $row = $result->fetch_array();
        $all_rows = $row['cont'];

        //  definisco il numero totale di pagine
        $all_pages = ceil($all_rows / $x_pag);

        // Calcolo da quale record iniziare
        $first = ($pag - 1) * $x_pag; 
        ?>

        <!-- Form di filtro -->
        <div class="filter-form">
            <form action="vis_login.php" method="POST" class="form-inline justify-content-center">
                <div class="form-group mx-sm-3 mb-2">
                    <label for="filtroSelect" class="mr-3 font-weight-bold">Visualizza:</label>
                    <select id="filtroSelect" name="filtro" class="form-control">
                        <option value="disattivato" <?php echo ($filtro == "") ? "selected" : ""; ?>>Tutti</option>
                        <option value="riusciti" <?php echo ($filtro == " WHERE USER IS NOT NULL") ? "selected" : ""; ?>>Login riusciti</option>
                        <option value="falliti" <?php echo ($filtro == " WHERE USER IS NULL") ? "selected" : ""; ?>>Login falliti</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-filter mr-2"></i>Applica filtro</button>
            </form>
        </div>

        <?php
        //query per l'elenco degli utenti
        $query = "SELECT USER, DATA, IP";
        $query .= " FROM login_logs";
        $query .= $filtro;
        $query .= " ORDER BY DATA DESC";
        $query .= " LIMIT $first, $x_pag";
        $result = $conn->query($query);

        if ($result->num_rows != 0) {
        ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover login-table">
                    <thead>
                        <tr>
                            <th scope="col" width="10%"><i class="fas fa-check-double mr-2"></i>Riuscito</th>
                            <th scope="col" width="30%"><i class="fas fa-user mr-2"></i>Utente</th>
                            <th scope="col" width="30%"><i class="fas fa-network-wired mr-2"></i>Indirizzo IP</th>
                            <th scope="col" width="30%"><i class="fas fa-calendar-alt mr-2"></i>Data accesso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $result->fetch_array()) {
                            $class = ($row['USER'] != null) ? "success-row" : "failure-row";
                            echo "<tr class='$class'>";
                            if($row['USER'] != null) {
                                echo "<td><span class='badge badge-success'><i class='fas fa-check-circle mr-1'></i>SI</span></td>";
                            } else {
                                echo "<td><span class='badge badge-danger'><i class='fas fa-times-circle mr-1'></i>NO</span></td>";
                            }
                            echo "<td>" . ($row['USER'] ?? '<em>Sconosciuto</em>') . "</td>";
                            echo "<td>$row[IP]</td>";
                            // Formatta la data in formato italiano GG/MM/AAAA HH:MM:SS
                            $data_italiana = date("d/m/Y H:i:s", strtotime($row['DATA']));
                            echo "<td>$data_italiana</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="stats-box text-center">
                <p class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Numero di accessi: <strong><?php echo $all_rows; ?></strong></p>
            </div>
        <?php
        } else {
            echo "<div class='alert alert-info text-center' role='alert'>
                  <i class='fas fa-info-circle fa-2x mb-3'></i><br>
                  Nessun accesso al sistema è presente nel database.
                  </div>";
        }
        ?>

        <!-- Paginazione -->
        <?php if ($all_rows > 0 && $all_pages > 1): ?>
        <nav aria-label="Navigazione pagine">
            <ul class="pagination">
                <?php
                // Prima pagina
                if ($pag > 1) {
                    echo "<li class='page-item'><a class='page-link' href='?pag=1'>&laquo; Prima</a></li>";
                } else {
                    echo "<li class='page-item disabled'><span class='page-link'>&laquo; Prima</span></li>";
                }
                
                // Pagine precedenti
                $start_page = max(1, $pag - 2);
                $end_page = min($all_pages, $pag + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $pag) {
                        echo "<li class='page-item active'><span class='page-link'>$i</span></li>";
                    } else {
                        echo "<li class='page-item'><a class='page-link' href='?pag=$i'>$i</a></li>";
                    }
                }
                
                // Ultima pagina
                if ($pag < $all_pages) {
                    echo "<li class='page-item'><a class='page-link' href='?pag=$all_pages'>Ultima &raquo;</a></li>";
                } else {
                    echo "<li class='page-item disabled'><span class='page-link'>Ultima &raquo;</span></li>";
                }
                ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <?php
    $result->free();
    $conn->close();
    ?>
</body>
</html>