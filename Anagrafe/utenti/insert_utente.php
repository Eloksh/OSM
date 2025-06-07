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
<body class="bg-light">
    <header><?php stampaNavbar(); ?></header>

    <div class="container mt-4">
        <h2 class="mb-4 text-center">Inserisci un Nuovo Utente</h2>
        
        <div class="card shadow-lg p-4">
            <form id="form" action="" method="POST">
                <!-- Username -->
                <div class="mb-3">
                    <label for="user" class="form-label">Username</label>
                    <input type="text" class="form-control" name="user" required>
                </div>

                <!-- Password -->
                <div class="mb-3 password-field">
                    <label for="psw" class="form-label">
                        Password
                    </label>
                    <div class="input-container">
                        <input type="password" class="form-control" name="psw1" id="psw" required>
                        <i class="bi bi-eye toggle-password" onclick="togglePasswordVisibility('psw', this)"></i>
                    </div>
                    <div id="error" class="form-text text-danger d-none">Password non sicura</div>
                </div>
                 <i class="bi bi-info-circle info-icon ms-2" data-bs-toggle="tooltip" 
                    data-bs-placement="right" title="La password deve contenere almeno 8 caratteri, una maiuscola, una minuscola, un numero e un simbolo."></i>

                <!-- Conferma Password -->
                <div class="mb-3 password-field">
                    <label for="psw2" class="form-label">Conferma Password</label>
                    <div class="input-container">
                        <input type="password" class="form-control" name="psw2" id="psw2" required>
                        <i class="bi bi-eye toggle-password" onclick="togglePasswordVisibility('psw2', this)"></i>
                    </div>
                </div>

                <!-- Tipo utente -->
                <div class="mb-3">
                    <label for="accesso" class="form-label">
                        Tipo di utente
                    </label>
                    <select name="accesso" class="form-select">
                        <option value="admin">Amministratore</option>
                        <option value="gestore">Gestore</option>
                        <option value="utente">Utente generico</option>
                    </select>
                </div>
                    <i class="bi bi-info-circle info-icon ms-2" data-bs-toggle="tooltip" 
                    data-bs-placement="right"
                    title="Amministratore: accesso completo. 
                    Gestore: non può creare utenti. Utente: solo statistiche."></i>
                <button type="button" class="btn btn-primary w-100 mt-3" onclick="PwChecker()">Conferma</button>
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
        // Inizializza i tooltip
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover focus'
                });
            });
        });

        function PwChecker() {
            const psw = document.getElementById("psw").value;
            const psw2 = document.getElementById("psw2").value;
            const error = document.getElementById("error");

            const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&.])[A-Za-z\d@$!%*#?&.]{8,}$/;

            if (!pattern.test(psw)) {
                error.classList.remove("d-none");
            } else if (psw !== psw2) {
                alert("Le password non corrispondono");
            } else {
                document.getElementById("form").submit();
            }
        }

        function togglePasswordVisibility(inputId, iconElement) {
            const passwordInput = document.getElementById(inputId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                iconElement.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                iconElement.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
        //per correggere errori della console di chrome
        window.onclick = null;
    </script>
</body>
</html>