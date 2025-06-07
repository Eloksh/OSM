<?php 
/*
*** Autore: Ferraiuolo
*** Descrizione: Area personale dell'utente (versione rimodernizzata con Bootstrap)
*** 25/03/2020 Ferraiuolo: creazione del file
*** 03/05/2025 Aggiornamento: implementazione responsive con Bootstrap
*/
$config_path = __DIR__;
$util = $config_path . '/../util.php';
require $util;
setup();
isLogged("amministratore");
?>
<!DOCTYPE html>
<html lang="it">
<?php stampaIntestazione(); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Personale</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/areap.css">
    <style>
        .info-icon-orange {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            background-color: orange;
            border-radius: 50%;
            color: white;
            font-size: 0.8rem;
            cursor: pointer;
            margin-left: 5px;
        }
    </style>
</head>

<body class="bg-light">
    <?php stampaNavbar(); ?>
    <?php
    $util2 = $config_path . '/../db/db_conn.php';
    require_once $util2;
    
    $query = "SELECT id_accesso, data_inizio_val";
    if (isset($_POST['cambiaPsw']))
        $query .= ", password, sale";
    $query .= " FROM utenti WHERE user='{$_SESSION['nome']}'";
    
    $result = $conn->query($query);
    $row = $result->fetch_array();
    ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="text-center mb-0">
                            <i class="fas fa-user me-2"></i>Area personale<i class="fas fa-user ms-2"></i>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Nome utente:</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p><?php echo $_SESSION['nome']; ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Data creazione account:</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                <?php
                                if (isset($row['data_inizio_val'])) {
                                    $dataFormattata = date('d-m-Y', strtotime($row['data_inizio_val']));
                                    echo $dataFormattata;
                                } else {
                                    echo "Non presente";
                                }
                                ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Tipo di permesso:</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p><?php echo $_SESSION['nome']; ?></p>
                            </div>
                        </div>
                        
                        <?php if (isset($_POST['formCambiaPsw']) || isset($_POST['cambiaPsw'])) { ?>
                            <form name="form" id="form" action="area_personale.php" method="POST" class="mt-4">
                                <div class="mb-3 row">
                                    <label for="pswOld" class="col-sm-4 col-form-label">Password attuale:</label>
                                    <div class="col-sm-8 input-group">
                                        <input type="password" class="form-control" id="pswOld" name="pswOld" required>
                                        <span class="input-group-text toggle-password" data-target="pswOld">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="pswNew1" class="col-sm-4 col-form-label">
                                        Nuova password:
                                         <!-- colore info da rivedere-->
                                        <span class="info-icon-orange" data-bs-toggle="modal" data-bs-target="#passwordRequirementsModal">
                                            <i class="fas fa-info"></i>
                                        </span>
                                    </label>
                                    <div class="col-sm-8 input-group">
                                        <input type="password" class="form-control" id="pswNew1" name="pswNew1" required>
                                        <span class="input-group-text toggle-password" data-target="pswNew1">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="pswNew2" class="col-sm-4 col-form-label">Ripeti password:</label>
                                    <div class="col-sm-8 input-group">
                                        <input type="password" class="form-control" id="pswNew2" name="pswNew2" required>
                                        <span class="input-group-text toggle-password" data-target="pswNew2">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary" name="cambiaPsw" value="Cambia password">
                                        <i class="fas fa-key me-2"></i>Cambia password
                                    </button>
                                </div>
                            </form>
                        <?php } else { 
                            if (!isset($_POST['cambiaPsw'])) { ?>
                                <form name="form" id="form" action="area_personale.php" method="POST" class="mt-4">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                        <button type="submit" class="btn btn-primary" name="formCambiaPsw" value="Cambia password">
                                            <i class="fas fa-key me-2"></i>Cambia password
                                        </button>
                                    </div>
                                </form>
                            <?php }
                        } ?>
                        
                        <?php
                        if (isset($_POST['cambiaPsw'])) {
                            $pswNew1 = hash('sha256', $_POST['pswNew1'] . $row['sale']);
                            $pswNew2 = hash('sha256', $_POST['pswNew2'] . $row['sale']);
                            $pswOld = hash('sha256', $_POST['pswOld'] . $row['sale']);
                            $pswAttuale = $row['password'];
                            
                            if ($pswAttuale == $pswOld) {
                                if ($pswNew1 == $pswNew2) {
                                    if (preg_match('#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#', $_POST['pswNew1']) == 1) {
                                        $bytes = my_random_bytes(10);
                                        $saleNuovo = (bin2hex($bytes));
                                        $codificata = hash('sha256', $_POST['pswNew1'] . $saleNuovo);
                                        
                                        // prepare
                                        $stmt = $conn->prepare("UPDATE utenti SET password=?, sale=? WHERE user=?");
                                        // bind
                                        $stmt->bind_param("sss", $codificata, $saleNuovo, $_SESSION['nome']);
                                        // execute
                                        $r = $stmt->execute();
                                        
                                        if ($r) {
                                            echo '<div class="alert alert-success mt-3" role="alert">
                                                <i class="fas fa-check-circle me-2"></i>Password cambiata con successo!
                                            </div>';
                                            echo '<meta http-equiv="refresh" content="2;url=area_personale.php">';
                                        } else {
                                            echo '<div class="alert alert-danger mt-3" role="alert">
                                                <i class="fas fa-exclamation-circle me-2"></i>Errore nell\'aggiornamento della password, si prega di riprovare.
                                            </div>';
                                        }
                                    } else {
                                        echo '<div class="alert alert-warning mt-3" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Password non valida: inserire una password di almeno 8 caratteri con un carattere maiuscolo, minuscolo, un numero e un carattere speciale!
                                        </div>';
                                    }
                                } else {
                                    echo '<div class="alert alert-warning mt-3" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Errore, le nuove password non corrispondono.
                                    </div>';
                                }
                            } else {
                                echo '<div class="alert alert-danger mt-3" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>Errore, la password attuale è incorretta.
                                </div>';
                            }
                        }
                        
                        $result->free();
                        $conn->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<!-- Modal per i requisiti della password -->
<div class="modal fade" id="passwordRequirementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Requisiti della password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                La password deve avere:
                <ul class="list-unstyled">
                    <li>Almeno <strong>8 caratteri</strong></li>
                    <li>Almeno <strong>1 carattere maiuscolo</strong> (A-Z)</li>
                    <li>Almeno <strong>1 carattere minuscolo</strong> (a-z)</li>
                    <li>Almeno <strong>1 numero</strong> (0-9)</li>
                    <li>Almeno <strong>1 carattere speciale</strong> (es. !@#$%)</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ho capito</button>
            </div>
        </div>
    </div>
</div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
   <!-- Alla fine del body, prima della chiusura </body> -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const target = this.dataset.target;
            const input = document.getElementById(target);
            const icon = this.querySelector('i');
            
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        });
    });

    // Form validation
    const form = document.getElementById('form');
    if (form) {
        form.addEventListener('submit', function(event) {
            const psw1 = document.getElementById('pswNew1');
            const psw2 = document.getElementById('pswNew2');
            
            if (psw1 && psw2 && psw1.value !== psw2.value) {
                alert("Le password non corrispondono!");
                event.preventDefault();
            }
        });
    }

    // Modal handling
    const infoIcons = document.querySelectorAll('.fa-info-circle');
    infoIcons.forEach(icon => {
        icon.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});

 // Remove any existing window.onclick handlers that might conflict
        window.onclick = null
</script>
   
</body>
</html>