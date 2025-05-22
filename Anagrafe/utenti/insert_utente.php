<?php
require_once("../db/db_conn.php");
require_once("../util.php");
setup();
isLogged("amministratore");
stampaIntestazione();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Utente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/inserisci_utente.css">
</head>
<body>
    <header><?php stampaNavbar(); ?></header>

    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <h2 class="text-center mb-4">Inserisci un Nuovo Utente</h2>

<form id="form" action="" method="POST">
    <div class="mb-3">
        <label for="user" class="form-label">Username</label>
        <input type="text" class="form-control" name="user" required>
    </div>

    <div class="mb-3 position-relative password-container">
        <label for="psw" class="form-label">Password</label>
        <input type="password" class="form-control" name="psw1" id="psw" required>
        <i class="bi bi-eye" id="toggle-password" onclick="togglePasswordVisibility('psw', 'toggle-password')"></i>
        <i class="bi bi-info-circle info-icon" data-bs-toggle="tooltip" title="La password deve contenere almeno 8 caratteri, una maiuscola, una minuscola, un numero e un simbolo."></i>
        <div id="error" class="form-text text-danger d-none">Password non sicura</div>
    </div>

    <div class="mb-3 position-relative password-container">
        <label for="psw2" class="form-label">Conferma Password</label>
        <input type="password" class="form-control" name="psw2" id="psw2" required>
        <i class="bi bi-eye" id="toggle-password2" onclick="togglePasswordVisibility('psw2', 'toggle-password2')"></i>
    </div>

    <div class="mb-3 position-relative">
        <label for="accesso" class="form-label">Tipo di utente</label>
        <select name="accesso" class="form-select">
            <option value="admin">Amministratore</option>
            <option value="gestore">Gestore</option>
            <option value="utente">Utente generico</option>
        </select>
        <i class="bi bi-info-circle info-icon" data-bs-toggle="tooltip" title="Amministratore: accesso completo. Gestore: non può creare utenti. Utente: solo statistiche."></i>
    </div>

    <button type="button" class="btn btn-primary w-100" onclick="PwChecker()">Conferma</button>
</form>

        </div>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $utente = $_POST["user"];
        $psw1 = mysqli_real_escape_string($conn, stripslashes($_POST["psw1"]));
        $psw2 = mysqli_real_escape_string($conn, stripslashes($_POST["psw2"]));
        $id_accesso = $_POST["accesso"];

        if ($psw1 !== $psw2) {
            echo "<script>alert('Le password non corrispondono');</script>";
        } elseif (strlen($psw1) < 8) {
            echo "<script>alert('Errore: la password è troppo corta');</script>";
        } else {
            $sale = bin2hex(random_bytes(10));
            $codificata = hash('sha256', $psw1 . $sale);
            $stmt = $conn->prepare("INSERT INTO utenti (user,password,DATA_INIZIO_VAL,id_accesso,sale) VALUES (?,?,?,?,?)");
            $data = date('Y-m-d');
            $stmt->bind_param("sssss", $utente, $codificata, $data, $id_accesso, $sale);
            if ($stmt->execute()) {
                echo "<script>alert('Utente registrato con successo');</script>";
            } else {
                echo "<script>alert('Errore: nome utente già in uso');</script>";
            }
        }
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(t => new bootstrap.Tooltip(t));

    function PwChecker() {
        const psw = document.getElementById("psw").value;
        const psw2 = document.getElementById("psw2").value;
        const error = document.getElementById("error");

        const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

        if (!pattern.test(psw)) {
            error.classList.remove("d-none");
        } else if (psw !== psw2) {
            alert("Le password non corrispondono");
        } else {
            document.getElementById("form").submit();
        }
    }

    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const passwordIcon = document.getElementById(iconId);
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
</body>
</html>