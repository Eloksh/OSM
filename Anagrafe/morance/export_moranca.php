<?php
/*
*** Input:
*** gest_persone.php
*** Output. excel.php
Questo file serve a scegliere la query da eseguire attraverso menù a tendina
Questo file serve a Scaricare in locale con estensione.xls una tabella ricevuta dal db dopo opportuna query a scelta dell'utente
*** 03/04/2020 M.Scursatone : Creazione file e prima implementazione
*/
$config_path = __DIR__;
$util1 = $config_path .'/../util.php';
require_once $util1;
setup();
isLogged("gestore");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Excel - Villaggio di Nague</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        
        .header-villaggio {
            text-align: center;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .header-villaggio h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0d6efd; /* lo stesso blu di Bootstrap primary */
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 800px;
        }
        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            margin-top: 2rem;
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            padding: 1.25rem 1.5rem;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-export {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .card-header h2 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
<?php 
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
stampaIntestazione(); 
stampaNavbar(); 
?>
<div class="text-center mt-4">
    <h1 class="display-5 fw-bold text-primary">Villaggio di Nague</h1>
</div>
<div class="container">
    <div class="card">
        <div class="card-header">
            <p class="mb-0">Export su file Excel delle moran&ccedil;as</p>
        </div>
        
        <div class="card-body">
            <form action="excel_moranca.php" method="post">
                <div class="mb-3">
                    <label for="zona" class="form-label">Selezionare la zona:</label>
                    <select name="zona" id="zona" class="form-select">
                        <option value="%" selected>Tutte</option>   
                        <option value="N">Nord</option>
                        <option value="O">Ovest</option>
                        <option value="S">Sud</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="file" class="form-label">Nome del file da scaricare:</label>
                    <div class="input-group">
                        <input type="text" name="file" id="file" class="form-control" 
                               placeholder="Scrivi un nome" required>
                        <span class="input-group-text">.xls</span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-export" name="invia">
                    <i class="fas fa-file-excel me-2"></i>Genera Excel
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>