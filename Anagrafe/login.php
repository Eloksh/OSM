<?php
// Autore: Ferraiuolo
// Descrizione: Login migliorato responsive
// Data ultima modifica: 21/05/2025 - Ottimizzazione responsive

require_once "db/db_conn.php";
require_once "util.php";
setup();

$_SESSION['loggato'] = false;
$error_message = ""; // Aggiungiamo una variabile per memorizzare i messaggi di errore

if (isset($_POST['user']) && isset($_POST['psw'])) {
    $utente = mysqli_real_escape_string($conn, stripslashes($_POST['user']));
    $psw = $_POST['psw']; // Non serve protezione SQL Injection perché sarà hashata

    // Pulizia record vecchi
    $conn->query("DELETE FROM login_logs WHERE DATA < NOW() - INTERVAL 1 MONTH AND USER IS NOT NULL");
    $conn->query("DELETE FROM login_logs WHERE DATA < NOW() - INTERVAL 1 DAY AND USER IS NULL");

    $ip = $_SERVER["REMOTE_ADDR"] === '::1' ? '127.0.0.1' : $_SERVER["REMOTE_ADDR"];
    $timestamp = time();
    $data_ora = date("Y-m-d H:i:s");
    $accesso = true;

    // Controllo tentativi di login
    $result = $conn->query("SELECT COUNT(*) AS tentativi FROM login_logs WHERE ip='$ip'");
    $row = $result->fetch_array();

    if ($row['tentativi'] > 2) {
        $result = $conn->query("SELECT data FROM login_logs WHERE ip='$ip' ORDER BY data DESC LIMIT 2,1");
        if ($row = $result->fetch_array()) {
            $primo_tentativo = strtotime($row["data"]);
            if (($timestamp - $primo_tentativo) < 30) {
                $accesso = false;
            }
        }
    }

    if ($accesso) {
        $stmt = $conn->prepare("SELECT * FROM utenti WHERE user = ?");
        $stmt->bind_param("s", $utente);
        $stmt->execute();
        $result = $stmt->get_result();
        $fin = $result->fetch_array();

        if ($fin) {
            $codificata = hash('sha256', $psw . $fin['SALE']);
            $stmt = $conn->prepare("SELECT * FROM utenti WHERE user = ? AND password = ?");
            $stmt->bind_param("ss", $utente, $codificata);
            $stmt->execute();
            $result = $stmt->get_result();
            $fin = $result->fetch_array();
        }

        if ($fin) {
            $conn->query("INSERT INTO login_logs(ip, data, user) VALUES ('$ip', '$data_ora', '$utente')");
            $_SESSION['login_time'] = $timestamp;
            $_SESSION['loggato'] = true;
            $_SESSION['tipo'] = $fin["ID_ACCESSO"];
            $_SESSION['nome'] = $fin["USER"];
            header("Location: index.php?welcome=true");
        } else {
            $conn->query("INSERT INTO login_logs(ip, data) VALUES ('$ip', '$data_ora')");
            $error_message = "Username e/o password sbagliati"; // Memorizziamo il messaggio di errore
        }
    } else {
        $error_message = "<div id='troppiTentativi'><p style='color:red;'>ERRORE: TROPPI TENTATIVI. ASPETTA <span id='timer'>" . (30 - ($timestamp - $primo_tentativo)) . "</span> SECONDI</p></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <?php stampaIntestazione(); ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./css/login.css">
    <!-- Aggiunto preconnect per migliorare il caricamento dei font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php stampaNavbar(); ?>

    <div class="container d-flex justify-content-center align-items-center">
        <div class="login-container">
            <h2>Login</h2>
            <?php 
            // Mostriamo il messaggio di errore qui, sotto il titolo "Login"
            if (!empty($error_message)) {
                echo '<div class="error-message">' . $error_message . '</div>';
            }
            ?>
            <form id="login" action="login.php" method="POST">
                <div class="mb-3">
                    <input type="text" class="form-control" name="user" placeholder="Username" required>
                </div>
                <div class="mb-3 password-container">
                    <input type="password" class="form-control" id="password" name="psw" placeholder="Password" required>
                    <i class="fa fa-eye" id="password-icon" onclick="togglePasswordVisibility()"></i>
                </div>
                <button type="submit" class="btn button">Login</button>
            </form>
            <p id="error"></p>
        </div>
    </div>

    <script>
        window.onload = function() {
            var tempo = document.getElementById("timer") ? document.getElementById("timer").textContent : 0;
            if (tempo > 0) {
                var timer = setInterval(function () {
                    tempo--;
                    document.getElementById("timer").textContent = tempo;
                    if (tempo <= 0) {
                        clearInterval(timer);
                        document.getElementById("troppiTentativi").textContent = "Ora puoi riprovare ad accedere";
                    }
                }, 1000);
            }
        };

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>