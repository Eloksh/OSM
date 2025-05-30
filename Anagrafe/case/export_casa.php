<?php
/*
*** gest_persone.php
*** Export su file Excel delle case
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
    <title>Export Excel - Villaggio di NTchangue</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
        }
        
        .export-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .export-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-top: 20px;
        }
        
        .page-title {
            color: var(--secondary-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 5px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-export {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            margin-top: 15px;
        }
        
        .btn-export:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .file-input-group {
            position: relative;
        }
        
        .file-extension {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #e9ecef;
            padding: 0 10px;
            border-radius: 0 5px 5px 0;
            height: calc(100% - 2px);
            display: flex;
            align-items: center;
            border-left: 1px solid #ced4da;
        }
        
        @media (max-width: 768px) {
            .export-container {
                padding: 15px;
            }
            
            .export-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container export-container">
        <?php stampaIntestazione(); ?>
        <?php stampaNavbar(); ?>
        
        <div class="export-card">
            <h2 class="page-title">Villaggio di NTchangue: Export su file Excel delle case</h2>
            
            <form action="excel_casa.php" method="post">
                <div class="mb-4">
                    <label for="zona" class="form-label">Selezionare la zona:</label>
                    <select name="zona" id="zona" class="form-select">
                        <option value="%">Tutte le zone</option>   
                        <option value="N">Nord</option>
                        <option value="O">Ovest</option>
                        <option value="S">Sud</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="file" class="form-label">Nome del file da scaricare:</label>
                    <div class="file-input-group">
                        <input type="text" name="file" id="file" class="form-control" 
                               placeholder="Inserisci il nome del file" required>
                        <span class="file-extension">.xls</span>
                    </div>
                    <small class="text-muted">Inserisci il nome senza estensione</small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-export">
                    <i class="fas fa-file-export me-2"></i>Genera File Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aggiunge l'estensione .xls al nome file se non presente
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file');
            if(fileInput.value && !fileInput.value.endsWith('.xls')) {
                fileInput.value = fileInput.value + '.xls';
            }
        });
    </script>
</body>
</html>