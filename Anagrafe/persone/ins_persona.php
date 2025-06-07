<?php
/*
*** ins_persona.php
*** Inserimento di una nuova persona
*/
$config_path = __DIR__;
$util1 = $config_path .'/../util.php';
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
require_once $util1;
setup();
isLogged("gestore");
$pag = $_SESSION['pag_p']['pag_p'];

// Query per le case disponibili
$query = "SELECT m.nome as nome_moranca, c.id as id_casa, c.nome as nome_casa,
          p.nominativo as capo_famiglia
          FROM morance m 
          INNER JOIN casa c ON m.id = c.id_moranca 
          INNER JOIN zone z ON z.cod = m.cod_zona 
          LEFT JOIN pers_casa pc ON c.id = pc.id_casa AND pc.cod_ruolo_pers_fam = 'CF'
          LEFT JOIN persone p ON p.id = pc.id_pers
          WHERE c.DATA_FINE_VAL is null
          ORDER BY c.nome ASC";
$case_result = $conn->query($query);

// Query per i ruoli
$ruoli_result = $conn->query("SELECT distinct cod, descrizione FROM ruolo_pers_fam");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserimento Nuova Persona</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
        }
        
        .form-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-top: 20px;
        }
        
        .page-title {
            color: var(--secondary-color);
            margin-bottom: 25px;
            font-weight: 600;
            text-align: center;
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
        
        .btn-submit {
            background-color: var(--primary-color);
            border: none;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-submit:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 20px;
            border-radius: 5px;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .back-link:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }
            
            .form-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <?php stampaIntestazione(); ?>
        <?php stampaNavbar(); ?>
        
        <div class="form-card">
            <h3 class="page-title">Inserimento di una nuova persona</h3>
            
            <form action="insert_persona.php" method="post" id="personaForm">
                <div class="mb-3">
                    <label for="nome_persona" class="form-label">Nominativo:</label>
                    <input type="text" class="form-control" id="nome_persona" name="nome_persona" 
                           placeholder="Inserire nome e cognome" required>
                </div>
                
                <div class="mb-3">
                    <label for="data_nascita" class="form-label">Data nascita:</label>
                    <input type="date" class="form-control" id="data_nascita" name="data_nascita" required>
                </div>
                
                <div class="mb-3">
                    <label for="sesso" class="form-label">Sesso:</label>
                    <select class="form-select" id="sesso" name="sesso" required>
                        <option value="" selected disabled>Seleziona sesso</option>
                        <option value="m">Maschio</option>
                        <option value="f">Femmina</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="id_casa_nuova" class="form-label">Residente nella casa:</label>
                    <select class="form-select" id="id_casa_nuova" name="id_casa_nuova" required>
                        <option value="" selected disabled>Seleziona una casa</option>
                        <?php while($row = $case_result->fetch_array()): ?>
                            <?php if($row['nome_casa'] != null || $row['nome_casa'] != ""): ?>
                                <?php 
                                    $myCapoFam = utf8_encode($row['capo_famiglia']);
                                    $myMoranca = utf8_encode($row['nome_moranca']);
                                    if ($myCapoFam == "") $myCapoFam = "Non Esiste";
                                ?>
                                <option value="<?= $row['id_casa'] ?>">
                                    Casa: <?= $row['nome_casa'] ?> 
                                    (Capo famiglia: <?= $myCapoFam ?>) 
                                    Moranca: <?= $myMoranca ?>
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="id_ruolo_nuovo" class="form-label">Ruolo:</label>
                    <select class="form-select" id="id_ruolo_nuovo" name="id_ruolo_nuovo" required>
                        <option value="" selected disabled>Seleziona un ruolo</option>
                        <?php while($row = $ruoli_result->fetch_array()): ?>
                            <?php if($row['cod'] != null): ?>
                                <option value="<?= $row['cod'] ?>"><?= $row['descrizione'] ?></option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="matricola" class="form-label">Matricola (se studente):</label>
                    <input type="text" class="form-control" id="matricola" name="matricola" 
                           placeholder="Inserire matricola">
                </div>
                
                <div class="mb-3">
                    <label for="inizio_matricola" class="form-label">Data inizio matricola:</label>
                    <input type="date" class="form-control" id="inizio_matricola" name="inizio_matricola">
                </div>
                
                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="fas fa-save me-2"></i>Conferma Inserimento
                </button>
            </form>
            
            <a href="gest_persone.php?pag=<?= $pag ?>" class="btn btn-secondary back-link">
                <i class="fas fa-arrow-left me-2"></i>Torna a gestione persone
            </a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validazione form lato client
        document.getElementById('personaForm').addEventListener('submit', function(e) {
            // Puoi aggiungere qui ulteriori validazioni se necessario
            console.log('Form validato con successo');
        });

          // Remove any existing window.onclick handlers that might conflict
        window.onclick = null;
    </script>
</body>
</html>