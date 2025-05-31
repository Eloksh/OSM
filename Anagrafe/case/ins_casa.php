<?php
/*
*** ins_casa.php
*** Richiamato da gest_casa.php
*** attiva insert_casa.php
*** 15/3/2020: A.Carlone
*** 25/02/2020  Ferraiuolo:  Modifica:rimosso selezione capofamiglia
*/
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
setup();
isLogged("gestore");
?>
<?php stampaIntestazione(); ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .form-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2.5rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        h3 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        
        .input-group-text {
            background-color: var(--light-color);
            border: 1px solid #e0e0e0;
        }
        
        .tooltip-icon {
            height: 20px;
            width: 30px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .tooltip-icon:hover {
            opacity: 0.8;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
            box-shadow: var(--box-shadow);
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
            box-shadow: var(--box-shadow);
        }
        
        .required-field::after {
            content: " *";
            color: #e63946;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .d-md-flex {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .me-md-2 {
                margin-right: 0 !important;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
<?php stampaNavbar(); ?>

<div class="container mt-4">
    <div class="form-container">
        <h3>Inserimento di una nuova casa</h3>
        
        <form action="insert_casa.php" name="form" id="form" method="POST">
            <div class="mb-4">
                <label for="name" class="form-label required-field">Nome casa:</label>
                <input type="text" class="form-control" name="nome" required>
            </div>
            
            <div class="mb-4">
                <label for="moranca" class="form-label required-field">Moranca:</label>
                <select class="form-select" name="add_moranca" required>
                    <option value=""></option>
                    <?php
                    $result = $conn->query("SELECT id, nome FROM morance");
                    $nr = $result->num_rows;
                    for($i = 0; $i < $nr; $i++) {
                        $row = $result->fetch_array();
                        if($row["nome"] != null || $row["nome"] != "") {
                            $myMor = utf8_encode($row['nome']);
                            echo "<option value='".$row['id']."'>".$myMor."</option>";
                        }
                    }
                    $result->free_result();
                    ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="mappa" class="form-label">Sulla mappa:</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="id_osm">
                    <span class="input-group-text" id="info" data-bs-toggle="tooltip" data-bs-placement="right" 
                          title="Identificativo della casa sulla mappa OpenStreetMap: 1. vai sulla mappa OSM, 2. cerca la casa, 3. clicca con il pulsante destro del mouse, scegli 'ricerca di elementi' 4. copia qui il numero dell'oggetto relativo (il numero senza #)">
                        <img src="../img/infoIcon.png" class="tooltip-icon">
                    </span>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href='gest_case.php?pag=<?php echo $pag; ?>' class="btn btn-secondary me-md-2">Torna a gestione case</a>
                <button type="submit" class="btn btn-primary">Conferma</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>