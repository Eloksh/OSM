<?php
/*
*** gest_persone.php
*** Export su file Excel delle persone
*/
$config_path = __DIR__;
$util1 = $config_path .'/../util.php';
require_once $util1;
setup();
isLogged("gestore");

// Connessione al database
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
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
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .export-container {
            max-width: 1200px;
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
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .section-title {
            color: var(--secondary-color);
            margin-bottom: 20px;
            font-weight: 500;
            text-align: center;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 8px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-item {
            flex: 1;
            min-width: 150px;
        }
        
        .form-control, .form-select {
            border-radius: 5px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
            width: 100%;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-export {
            background-color: var(--primary-color);
            border: none;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .btn-export:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .file-input-group {
            position: relative;
            margin-bottom: 20px;
        }
        
        .file-extension {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            background-color: #e9ecef;
            padding: 0 15px;
            border-radius: 0 5px 5px 0;
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
            
            .filter-item {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="export-container">
        <?php stampaIntestazione(); ?>
        <?php stampaNavbar(); ?>
        
        <div class="export-card">
            <h1 class="page-title">Villaggio di NTchangue</h1>
            <h2 class="section-title">Export su file Excel delle persone</h2>
            
            <form action="excel_persona.php" method="post">
                <div class="form-group">
                    <h3 class="form-label">Selezione del tipo di dato da esportare</h3>
                </div>
                
                <div class="filter-row">
                    <div class="filter-item">
                        <label for="zona" class="form-label">Zona:</label>
                        <select name="zona" id="zona" class="form-select">
                            <option value="%">Tutte le zone</option>   
                            <option value="N">Nord</option>
                            <option value="O">Ovest</option>
                            <option value="S">Sud</option>
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <label for="sesso" class="form-label">Sesso:</label>
                        <select name="sesso" id="sesso" class="form-select">
                            <option value="%">Tutti</option>
                            <option value="m">Maschi</option>
                            <option value="f">Femmine</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-row">
                    <div class="filter-item">
                        <label for="eta" class="form-label">Età:</label>
                        <select name="eta" id="eta" class="form-select">
                            <option value="%">Tutte</option>
                            <option value="minorenni">Minorenni</option>     
                            <option value="maggiorenni">Maggiorenni</option>
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <label for="order" class="form-label">Ordinato per:</label>
                        <select name="order" id="order" class="form-select">
                            <option value="nominativo">Nome</option>
                            <option value="data_nascita">Età</option>
                            <option value="id">ID</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
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
        // Aggiunge automaticamente l'estensione .xls se non presente
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file');
            if(fileInput.value && !fileInput.value.endsWith('.xls')) {
                fileInput.value = fileInput.value + '.xls';
            }
        });
    </script>
</body>
</html>