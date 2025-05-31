<?php

$config_path = __DIR__;
$util1 = $config_path .'/../util.php';
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
require_once $util1;
setup();
isLogged("gestore");
$pag = $_SESSION['pag_m']['pag_m'] ?? 1;
$id_moranca = $_POST["id_moranca"] ?? null;

if (!$id_moranca) {
    die("ID morança non specificato");
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MODIFICA MORANCA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
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
        .page-header {
            background-color: white;
            padding: 20px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <?php stampaIntestazione(); ?>
    <?php stampaNavbar(); ?>

    <!-- Titolo sopra al container -->
    <div class="page-header">
        <div class="container">
            <h1>Modifica Morança: </h1>
        </div>
    </div>
    <div class="container">
        <div class="form-container">
            <?php
            $query = "SELECT m.ID id, m.NOME 'nome_moranca', m.cod_zona, z.nome zona, m.id_osm 
                     FROM morance m INNER JOIN zone z ON m.cod_zona = z.cod 
                     WHERE id='$id_moranca'";
            $result = $conn->query($query);
            $row = $result->fetch_array();
            
            $moranca = utf8_encode($row['nome_moranca']);
            $cod_zona = $row['cod_zona'];
            $zona = $row['zona'];
            $id_osm = $row['id_osm'];
            ?>
            
           <h2 class="mb-4">Morança -> <?php echo htmlspecialchars($moranca); ?> (ID = <?php echo htmlspecialchars($row['id']); ?>)</h2>
            
            <form action="modifica_moranca.php" method="post" class="mb-5">
                <input type="hidden" name="id_moranca" value="<?php echo htmlspecialchars($id_moranca); ?>">
                
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome morança:</label>
                    <input type="text" class="form-control" name="nome_moranca" value="<?php echo htmlspecialchars($moranca); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="zona" class="form-label">Zona:</label>
                    <select name="cod_zona" class="form-select">
                        <?php
                        $result = $conn->query("SELECT * FROM zone");
                        while($row = $result->fetch_array()) {
                            $selected = ($cod_zona == $row["COD"]) ? "selected" : "";
                            echo "<option value='".htmlspecialchars($row["COD"])."' $selected>".htmlspecialchars($row["NOME"])."</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="mappa" class="form-label">ID su OpenStreetMap:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="id_osm" value="<?php echo htmlspecialchars($id_osm); ?>">
                        <span class="input-group-text tooltip-icon" data-bs-toggle="tooltip" title="Identificativo della morança sulla mappa OpenStreetMap">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="info-text">
                        1. Vai sulla mappa OSM<br>
                        2. Cerca la morança<br>
                        3. Clicca con il pulsante destro del mouse, scegli 'Ricerca di elementi'<br>
                        4. Copia qui il numero dell'oggetto relativo (senza #)
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Modifica</button>
            </form>
            
            <h3 class="mb-3">Modifica la foto della morança</h3>
            
            <?php
            // Gestione upload foto
            if(isset($_POST["caricaFoto"])) {
                $target_dir = "immagini/";
                $target_file = $target_dir . $id_moranca . '.' . pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);
                $flagUpload = true;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    if ($_FILES["fileToUpload"]["size"] > 500000) {
                        echo '<div class="alert alert-danger">Errore: l\'immagine è troppo grande (max 500KB)</div>';
                        $flagUpload = false;
                    }
                    
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                        echo '<div class="alert alert-danger">Errore: sono consentiti solo file JPG, PNG o JPEG</div>';
                        $flagUpload = false;
                    }
                    
                    if ($flagUpload) {
                        $files = glob($target_dir . $id_moranca . '.*');
                        foreach ($files as $file) {
                            unlink($file);
                        }
                        
                        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                            echo '<div class="alert alert-success">L\'immagine è stata caricata con successo</div>';
                        }
                    }
                } else {
                    echo '<div class="alert alert-danger">Errore: il file caricato non è un\'immagine valida</div>';
                }
            }
            
            // Gestione eliminazione foto
            if(isset($_POST["eliminaFoto"])) {
                $target_dir = "immagini/";
                $files = glob($target_dir . $id_moranca . '.*');
                foreach ($files as $file) {
                    unlink($file);
                }
                echo '<div class="alert alert-success">Foto eliminata con successo</div>';
            }
            ?>
            
            <form action="mod_moranca.php" method="post" enctype="multipart/form-data" class="mb-4">
                <input type="hidden" name="id_moranca" value="<?php echo htmlspecialchars($id_moranca); ?>">
                
                <div class="mb-3">
                    <label for="fileToUpload" class="form-label">Seleziona una foto da caricare:</label>
                    <input class="form-control" type="file" name="fileToUpload" id="fileToUpload" required>
                </div>
                
                <button type="submit" class="btn btn-primary" name="caricaFoto">Carica foto</button>
            </form>
            
            <?php
            $immagine = glob('immagini/' . $id_moranca . '.*');
            if(!empty($immagine)) {
                echo '<div class="mb-3">';
                echo '<p class="fw-bold">Foto attuale:</p>';
                echo '<img src="' . htmlspecialchars($immagine[0]) . '" class="img-thumbnail image-preview">';
                
                echo '<form action="mod_moranca.php" method="post">';
                echo '<input type="hidden" name="id_moranca" value="' . htmlspecialchars($id_moranca) . '">';
                echo '<button type="submit" class="btn btn-danger" name="eliminaFoto">Elimina foto</button>';
                echo '</form>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-info">Attualmente non è presente alcuna foto</div>';
            }
            ?>
            
            <a href="gest_morance.php?pag=<?php echo htmlspecialchars($pag); ?>" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left me-2"></i>Torna a gestione morance
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