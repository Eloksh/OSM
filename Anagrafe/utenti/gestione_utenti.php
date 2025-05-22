<?php
$config_path = __DIR__;
require_once $config_path . '/../util.php';
require_once $config_path . '/../db/db_conn.php';
setup();
isLogged("amministratore");
?>
<!DOCTYPE html>
<html lang="it">
<?php stampaIntestazione(); ?>
<head>
    <meta charset="UTF-8">
    <title>Gestione Utenti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/OSM/Anagrafe/css/style_utenti.css">
    <style>
   
    </style>
</head>

<body class="bg-light">
<?php stampaNavbar(); ?>

<?php
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

// Conta utenti per la paginazione
$query = "SELECT COUNT(user) as cont FROM utenti";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$all_rows = $row['cont'];
$all_pages = ceil($all_rows / $x_pag);

echo "<div class='container'>";
echo "<h2 class='text-center'><i class='fa fa-user'></i> Elenco Utenti <i class='fa fa-user'></i></h2>";

$perm = match ($_SESSION['tipo']) {
    'admin' => 'amministratore',
    'gestore' => 'gestore',
    'utente' => 'utente generico',
    default => 'sconosciuto'
};

echo "<p>Utente collegato: <strong>{$_SESSION['nome']}</strong> / permesso: <strong>$perm</strong></p>";
echo "<a href='vis_login.php' class='btn btn-outline-primary mb-3'><i class='fa fa-eye'></i> Visualizza accessi</a> ";
echo "<a href='insert_utente.php' class='btn btn-success mb-3 ms-2'><i class='fa fa-user-plus'></i> Inserisci nuovo utente</a>";

// Filtro tipo utente
echo "<form method='POST' class='row g-3'>";
echo "<div class='col-md-4'>";
echo "<label class='form-label'>Seleziona permessi:</label>";
echo "<select name='tipo' class='form-select'>";
echo "<option value='tutti'>Tutti</option>";
$result = $conn->query("SELECT DISTINCT id_accesso FROM utenti");
while ($row = $result->fetch_assoc()) {
    $selected = (isset($_POST['tipo']) && $_POST['tipo'] === $row['id_accesso']) ? "selected" : "";
    echo "<option value='{$row['id_accesso']}' $selected>{$row['id_accesso']}</option>";
}
echo "</select></div>";
echo "<div class='col-md-2 d-flex align-items-end'>";
echo "<button type='submit' class='btn btn-primary'>Conferma</button></div>";
echo "</form><br>";

// Query utenti
$query = "SELECT user, id_accesso, data_inizio_val FROM utenti";
if (isset($_POST['tipo']) && $_POST['tipo'] != 'tutti') {
    $tipo = $_POST['tipo'];
    $query .= " WHERE id_accesso = '$tipo'";
}
$query .= " ORDER BY user ASC LIMIT $first, $x_pag";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'><table class='table table-bordered table-hover text-center'>";
    echo "<thead class='table-dark'><tr><th>Utente</th><th>Permessi</th><th>Data creazione</th><th>Elimina</th></tr></thead><tbody>";

    while ($row = $result->fetch_assoc()) {
        $perm = match ($row['id_accesso']) {
            'admin' => 'amministratore (accesso completo)',
            'gestore' => 'gestore (non può registrare nuovi utenti)',
            'utente' => 'utente generico (può visualizzare solo le statistiche)',
            default => 'sconosciuto'
        };
        echo "<tr>";
        echo "<td>{$row['user']}</td>";
        echo "<td>{$perm}</td>";
        echo "<td>{$row['data_inizio_val']}</td>";
        echo "<td>
                <form method='post' action='gestione_utenti.php' onsubmit=\"return confirm('Confermi l\\'eliminazione?');\">
                    <input type='hidden' name='idElimina' value='{$row['user']}'>
                    <input type='hidden' name='si' value='1'>
                    <button type='submit' class='btn btn-danger'><i class='fa fa-trash'></i></button>
                </form>
              </td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "<div class='alert alert-warning'>Nessun utente presente nel database.</div>";
}

echo "<p>Numero di utenti totali: <strong>$all_rows</strong></p>";

// Paginazione
echo "<nav><ul class='pagination justify-content-center'>";
for ($i = 1; $i <= $all_pages; $i++) {
    $active = ($i == $pag) ? "active" : "";
    echo "<li class='page-item $active'><a class='page-link' href='gestione_utenti.php?pag=$i'>$i</a></li>";
}
echo "</ul></nav>";

echo "</div>"; // chiude container
$conn->close();
?>
</body>
</html>