<?php
$config_path = __DIR__;
require_once $config_path . '/../util.php';
require_once $config_path . '/../db/db_conn.php';
setup();
isLogged("amministratore");

// Funzione per formattare la data in formato italiano GG/MM/AAAA
function formatDataItaliana($data) {
    if (empty($data) || $data == '0000-00-00') {
        return '';
    }
    $date = new DateTime($data);
    return $date->format('d/m/Y');
}

$x_pag = 10;
$pag = isset($_GET['pag']) && is_numeric($_GET['pag']) ? (int)$_GET['pag'] : 1;
$first = ($pag - 1) * $x_pag;

if (isset($_POST['idElimina']) && isset($_POST['si'])) {
    if ($_POST['idElimina'] != $_SESSION['nome']) {
        $query = "DELETE FROM utenti WHERE user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $_POST['idElimina']);
        if ($stmt->execute()) {
            echo "<script>alert('Utente eliminato con successo');</script>";
        }
    } else {
        echo "<script>alert('Impossibile eliminare l\\'utente attualmente in uso');</script>";
    }
}

// Inizializzazione variabili per evitare warning
$all_rows = 0;
$all_pages = 1;
$result = null;

// Conta utenti per la paginazione
$query = "SELECT COUNT(user) as cont FROM utenti";
$count_result = $conn->query($query);
if ($count_result) {
    $row = $count_result->fetch_assoc();
    $all_rows = $row['cont'];
    $all_pages = ceil($all_rows / $x_pag);
}

// Query utenti con gestione errori
$query = "SELECT user, id_accesso, data_inizio_val FROM utenti";
if (isset($_POST['tipo']) && $_POST['tipo'] != 'tutti') {
    $tipo = $_POST['tipo'];
    $query .= " WHERE id_accesso = '$tipo'";
}
$query .= " ORDER BY user ASC LIMIT $first, $x_pag";
$result = $conn->query($query);

$perm = match ($_SESSION['tipo']) {
    'admin' => 'amministratore',
    'gestore' => 'gestore',
    'utente' => 'utente generico',
    default => 'sconosciuto'
};
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/style_utenti.css">
    <style>
        /* Mobile-first responsive styles */
        .btn-group-mobile {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 1rem;
        }
        
        @media (min-width: 768px) {
            .btn-group-mobile {
                flex-direction: row;
                align-items: center;
            }
        }
        
        /* Table optimizations */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-fit {
            width: 100% !important;
            min-width: 300px;
        }
        
        .table-fit th,
        .table-fit td {
            white-space: normal !important;
            word-wrap: break-word;
            padding: 0.75rem 0.5rem;
        }
        
        /* Permission column styling */
        .permission-cell {
            max-width: 200px;
        }
        
        /* Date column styling */
        .date-cell {
            white-space: nowrap;
            width: 100px;
        }
        
        /* Mobile-specific optimizations */
        @media (max-width: 767px) {
            .permission-cell {
                max-width: 150px;
            }
            
            .table-fit th, 
            .table-fit td {
                font-size: 0.85rem;
                padding: 0.5rem 0.3rem;
            }
            
            .btn-sm-mobile {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }
        }
        
        /* Form grid adjustments */
        .filter-form .col-action {
            margin-top: 0.5rem;
        }
        
        @media (max-width: 767px) {
            .filter-form .col-action {
                margin-top: 0;
            }
        }
        
        /* Button spacing */
        .action-buttons {
            gap: 0.5rem;
        }
        
        /* Hide non-essential text on mobile */
        @media (max-width: 575px) {
            .permission-details {
                display: none;
            }
        }
        
        /* Additional styles for error prevention */
        .user-count {
            font-weight: bold;
            margin: 15px 0;
        }
        
        .pagination-container {
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-light">
<?php stampaIntestazione(); ?>
<?php stampaNavbar(); ?>

<div class="container">
    <h2 class="text-center"><i class="fa fa-user"></i> Elenco Utenti <i class="fa fa-user"></i></h2>

    <p>Utente collegato: <strong><?php echo $_SESSION['nome']; ?></strong> / permesso: <strong><?php echo $perm; ?></strong></p>
    
    <div class="btn-group-mobile">
        <a href="vis_login.php" class="btn btn-outline-primary mb-3"><i class="fa fa-eye"></i> Visualizza accessi</a>
        <a href="insert_utente.php" class="btn btn-success mb-3 ms-md-2"><i class="fa fa-user-plus"></i> Inserisci nuovo utente</a>
    </div>

    <!-- Filtro tipo utente -->
    <form method="POST" class="row g-3 filter-form">
        <div class="col-md-4">
            <label class="form-label">Seleziona permessi:</label>
            <select name="tipo" class="form-select">
                <option value="tutti">Tutti</option>
                <?php
                $types_result = $conn->query("SELECT DISTINCT id_accesso FROM utenti");
                if ($types_result) {
                    while ($row = $types_result->fetch_assoc()) {
                        $selected = (isset($_POST['tipo']) && $_POST['tipo'] === $row['id_accesso']) ? "selected" : "";
                        echo "<option value='{$row['id_accesso']}' $selected>{$row['id_accesso']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Conferma</button>
        </div>
    </form>
    <br>

    <?php
    if ($result && $result->num_rows > 0) {
        echo "<div class='table-responsive'><table class='table table-bordered table-hover text-center table-fit'>";
        echo "<thead class='table-dark'><tr><th>Utente</th><th class='permission-cell'>Permessi</th><th class='date-cell'>Data creazione</th><th>Elimina</th></tr></thead><tbody>";

        while ($row = $result->fetch_assoc()) {
            $perm = match ($row['id_accesso']) {
                'admin' => '<span class="d-inline">amministratore</span> <span class="permission-details">(accesso completo)</span>',
                'gestore' => '<span class="d-inline">gestore</span> <span class="permission-details">(non può registrare nuovi utenti)</span>',
                'utente' => '<span class="d-inline">utente generico</span> <span class="permission-details">(può visualizzare solo le statistiche)</span>',
                default => 'sconosciuto'
            };
            $data_creazione = formatDataItaliana($row['data_inizio_val']);
            
            echo "<tr>";
            echo "<td>{$row['user']}</td>";
            echo "<td class='permission-cell'>{$perm}</td>";
            echo "<td class='date-cell'>{$data_creazione}</td>";
            echo "<td>
                    <form method='post' action='gestione_utenti.php' onsubmit=\"return confirm('Confermi l\\'eliminazione?');\">
                        <input type='hidden' name='idElimina' value='{$row['user']}'>
                        <input type='hidden' name='si' value='1'>
                        <button type='submit' class='btn btn-danger btn-sm-mobile'><i class='fa fa-trash'></i></button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody></table></div>";
    } else {
        echo "<div class='alert alert-warning'>Nessun utente presente nel database.</div>";
    }

    echo "<p class='user-count'>Numero di utenti totali: <strong>$all_rows</strong></p>";

    // Paginazione
    if ($all_pages > 1) {
        echo "<nav class='pagination-container'><ul class='pagination justify-content-center'>";
        for ($i = 1; $i <= $all_pages; $i++) {
            $active = ($i == $pag) ? "active" : "";
            echo "<li class='page-item $active'><a class='page-link' href='gestione_utenti.php?pag=$i'>$i</a></li>";
        }
        echo "</ul></nav>";
    }
    ?>
</div>

<?php
if (isset($conn)) {
    $conn->close();
}
?>
</body>
</html>