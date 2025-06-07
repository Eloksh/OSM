<?php
/*
*** mod_casa.php
*** Modifica dati di una casa
*/
$config_path = __DIR__;
$util1 = "../util.php";
$util2 = "../db/db_conn.php";
require_once $util2;
require_once $util1;
setup();
isLogged("gestore");
$pag = $_SESSION['pag_c']['pag_c'] ?? 1;
$id_casa = $_POST["id_casa"] ?? null;

if (!$id_casa) {
    die("ID casa non specificato");
}

// Recupera dati della casa
$query = "SELECT c.id, c.nome as nome_casa, z.nome zona, c.id_moranca as id_moranca,
          m.nome nome_moranca, p.id as id_capo_famiglia, p.nominativo as capo_famiglia,
          c.id_osm, c.lat, c.lon, c.data_inizio_val data_inizio, c.data_fine_val as data_fine
          FROM morance m 
          INNER JOIN casa c ON m.id = c.id_moranca 
          INNER JOIN zone z ON z.cod = m.cod_zona 
          LEFT JOIN pers_casa pc ON c.id = pc.id_casa AND pc.cod_ruolo_pers_fam = 'CF'
          LEFT JOIN persone p ON p.id = pc.id_pers
          WHERE c.id = ? AND c.data_fine_val is null";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_casa);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();

if (!$row) {
    die("Casa non trovata");
}

$nome_casa = htmlspecialchars($row["nome_casa"]);
$data_inizio = htmlspecialchars($row["data_inizio"]);
$data_fine = htmlspecialchars($row["data_fine"]);
$id_moranca = htmlspecialchars($row["id_moranca"]); 
$lat = $row["lat"] ?? 0;
$lon = $row["lon"] ?? 0;
$nome_moranca = htmlspecialchars(utf8_encode($row['nome_moranca']));
$id_osm = $row["id_osm"] ?? 0;
$capo_famiglia = htmlspecialchars($row["capo_famiglia"]);
$id_capo_famiglia = $row["id_capo_famiglia"];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Casa</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding: 0;
            margin: 0;
        }
        .page-header {
            background-color: white;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .form-container {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 600;
            margin-top: 10px;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 8px 20px;
            margin-top: 15px;
        }
        .image-preview {
            max-width: 200px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .tooltip-icon {
            cursor: help;
            margin-left: 5px;
            color: #6c757d;
        }
        .info-text {
            display: none;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 5px;
            border-left: 3px solid #0d6efd;
        }
        .coordinate-label {
            margin-bottom: 0.2rem !important;
        }
        .coordinate-input {
            margin-top: 0.2rem !important;
        }
    </style>
</head>
<body>
    <?php stampaIntestazione(); ?>
    <?php stampaNavbar(); ?>
    
    <!-- Titolo sopra al container -->
    <div class="page-header">
        <div class="container">
            <h1>Modifica Casa: </h1>
        </div>
    </div>
    
    <div class="container py-4">
        <div class="form-container">            
            <!-- Form modifica dati casa -->
            <form action="modifica_casa.php" method="POST" class="mb-5">
                <input type="hidden" name="id_casa" value="<?php echo $id_casa; ?>">
                <input type="hidden" name="data_inizio" value="<?php echo $data_inizio; ?>">
                <input type="hidden" name="data_fine" value="<?php echo $data_fine; ?>">
                 
                <h2><?php echo $nome_casa; ?> (ID: <?php echo $id_casa; ?>)</h2>

                 <div class="mb-3">
                    <label for="nome_casa" class="form-label">Nome casa:</label>
                    <input type="text" class="form-control" name="nome_casa" value="<?php echo $nome_casa; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="moranca" class="form-label">Morança:</label>
                    <select name="moranca" class="form-select" required>
                        <option value="<?php echo $id_moranca; ?>"><?php echo $nome_moranca; ?></option>
                        <?php
                        $query = "SELECT id, nome FROM morance WHERE nome != ? ORDER BY nome ASC";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $nome_moranca);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        while($row = $result->fetch_array()) {
                            echo '<option value="'.htmlspecialchars($row["id"]).'">'.htmlspecialchars(utf8_encode($row["nome"])).'</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Coordinate:</label>
                    <div class="row g-1">
                        <div class="col-md-6">
                            <label for="lat" class="form-label coordinate-label">Latitudine:</label>
                            <input type="number" class="form-control coordinate-input" name="lat" value="<?php echo $lat; ?>" step="any" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="lon" class="form-label coordinate-label">Longitudine:</label>
                            <input type="number" class="form-control coordinate-input" name="lon" value="<?php echo $lon; ?>" step="any" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="id_osm" class="form-label">ID OpenStreetMap:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="id_osm" value="<?php echo $id_osm; ?>">
                        <span class="input-group-text tooltip-icon" data-bs-toggle="tooltip" title="Identificativo della casa sulla mappa OpenStreetMap">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="info-text">
                        1. Vai sulla mappa OSM<br>
                        2. Cerca la casa<br>
                        3. Clicca con il pulsante destro del mouse, scegli 'Ricerca di elementi'<br>
                        4. Copia qui il numero dell'oggetto relativo (senza #)
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Modifica</button>
            </form>
            
            <!-- Sezione modifica foto -->
            <h3 class="mb-3">Modifica la foto della casa</h3>
            
            <?php
            // Gestione upload foto
            if(isset($_POST["caricaFoto"])) {
                $target_dir = "immagini/";
                $target_file = $target_dir . $id_casa . '.' . pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);
                $uploadOk = true;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    if ($_FILES["fileToUpload"]["size"] > 500000) {
                        echo '<div class="alert alert-danger">Errore: l\'immagine è troppo grande (max 500KB)</div>';
                        $uploadOk = false;
                    }
                    
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                        echo '<div class="alert alert-danger">Errore: sono consentiti solo file JPG, PNG o JPEG</div>';
                        $uploadOk = false;
                    }
                    
                    if ($uploadOk) {
                        $files = glob($target_dir . $id_casa . '.*');
                        foreach ($files as $file) {
                            unlink($file);
                        }
                        
                        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                            echo '<div class="alert alert-success">L\'immagine è stata caricata con successo</div>';
                        } else {
                            echo '<div class="alert alert-danger">Si è verificato un errore durante il caricamento</div>';
                        }
                    }
                } else {
                    echo '<div class="alert alert-danger">Errore: il file caricato non è un\'immagine valida</div>';
                }
            }
            
            // Gestione eliminazione foto
            if(isset($_POST["eliminaFoto"])) {
                $target_dir = "immagini/";
                $files = glob($target_dir . $id_casa . '.*');
                foreach ($files as $file) {
                    if(unlink($file)) {
                        echo '<div class="alert alert-success">Foto eliminata con successo</div>';
                    } else {
                        echo '<div class="alert alert-danger">Errore durante l\'eliminazione della foto</div>';
                    }
                }
            }
            ?>
            
            <form action="mod_casa.php" method="post" enctype="multipart/form-data" class="mb-4">
                <input type="hidden" name="id_casa" value="<?php echo $id_casa; ?>">
                
                <div class="mb-3">
                    <label for="fileToUpload" class="form-label">Seleziona una foto da caricare:</label>
                    <input class="form-control" type="file" name="fileToUpload" id="fileToUpload" required>
                </div>
                
                <button type="submit" class="btn btn-primary" name="caricaFoto">Carica foto</button>
            </form>
            
            <?php
            $immagine = glob('immagini/' . $id_casa . '.*');
            if(!empty($immagine)) {
                echo '<div class="mb-3">';
                echo '<p class="fw-bold">Foto attuale:</p>';
                echo '<img src="' . htmlspecialchars($immagine[0]) . '" class="img-thumbnail image-preview">';
                
                echo '<form action="mod_casa.php" method="post">';
                echo '<input type="hidden" name="id_casa" value="' . $id_casa . '">';
                echo '<button type="submit" class="btn btn-danger" name="eliminaFoto">Elimina foto</button>';
                echo '</form>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-info">Attualmente non è presente alcuna foto</div>';
            }
            ?>
            
            <a href="gest_case.php?pag=<?php echo $pag; ?>" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left me-2"></i>Torna a gestione case
            </a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Abilita i tooltip di Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Mostra/nascondi informazioni aggiuntive
        document.querySelector('.tooltip-icon').addEventListener('click', function() {
            const infoText = document.querySelector('.info-text');
            infoText.style.display = infoText.style.display === 'block' ? 'none' : 'block';
        });
    </script>
</body>
</html>