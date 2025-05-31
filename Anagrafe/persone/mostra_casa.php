<?php
/*
06/04/2020:Ferraiuolo: aggiunta immagine e id osm
Descrizione:mostra informazioni della casa in cui risiede la persona selezionata
*/
$config_path = __DIR__;
$util = $config_path .'/../util.php';
require $util;
setup();
isLogged("gestore");
$pag = $_SESSION['pag_p']['pag_p'] ?? 1; // Imposta 1 come valore di default
unset($_SESSION['pag_p']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .container-main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .house-header {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .house-title {
            color: #0d6efd;
            font-weight: 600;
        }
        .map-icon {
            color: #dc3545;
            margin-left: 5px;
        }
        .table-responsive {
            margin: 20px 0;
        }
        .table th {
            background-color: #0d6efd;
            color: white;
        }
        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #0d6efd;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php stampaIntestazione(); ?>
    <?php stampaNavbar(); ?>
    
    <div class="container container-main">
        <!-- Modal for images -->
        <div id="myModal" class="modal">
            <span class="close">&times;</span>
            <img class="modal-content" id="img01">
        </div>

        <?php
        $util2 = $config_path .'/../db/db_conn.php';
        require_once $util2;
        
        $id_persona = $_POST["id_persona"];

        // selezione della casa in cui abita la persona passata in input
        $query1 = "SELECT c.id id_casa, c.nome nome_casa, c.id_moranca id_moranca, m.nome nome_moranca, z.nome nome_zona, c.id_osm
                  FROM casa c
                  INNER JOIN morance m ON c.id_moranca = m.id
                  INNER JOIN pers_casa pc ON c.id = pc.id_casa
                  INNER JOIN persone p ON p.id = pc.id_pers
                  INNER JOIN zone z ON z.cod = m.cod_zona
                  WHERE p.id = $id_persona";

        $result1 = $conn->query($query1);
        $row1 = $result1->fetch_array();
        $id_casa = $row1['id_casa'];
        $mystr = utf8_encode($row1['nome_moranca']);
        $osm_link = "https://www.openstreetmap.org/way/$row1[id_osm]";
        ?>
  <h3 class="house-title" style="color: black; margin-bottom: 10px;">Abitanti della casa: <?php echo $row1['nome_casa']; ?> (id=<?php echo $id_casa; ?>)</h3>
        <div class="house-header" style="background-color: white; border-bottom: 1px solid #dee2e6;">
            <p class="mb-1" style="color:rgb(0, 0, 0);">
                <strong>Moranca</strong> <?php echo $mystr; ?> (id=<?php echo $row1['id_moranca']; ?>), 
               <strong>Zona:</strong> <?php echo $row1['nome_zona']; ?> - 
                <?php if ($row1['id_osm'] != null && $row1['id_osm'] != "0"): ?>
                    <strong>Mappa:</strong>  <?php echo $row1['id_osm']; ?>
                    <a href="<?php echo $osm_link; ?>" target="_blank" class="map-icon">
                        <i class="fa fa-map-marker"></i>
                    </a>
                <?php else: ?>
                    <strong>Mappa: </strong> non presente
                <?php endif; ?>
            </p>
        </div>

        <?php
        // elenco delle persone che abitano in quella casa
        $query = "SELECT c.id, c.nome, z.nome zona, c.id_moranca, m.nome nome_moranca, 
                 p.id id_pers, p.nominativo, pc.cod_ruolo_pers_fam, rpf.descrizione desc_ruolo
                 FROM casa c 
                 INNER JOIN morance m ON c.id_moranca = m.id
                 INNER JOIN zone z ON z.cod = m.cod_zona
                 INNER JOIN pers_casa pc ON pc.id_casa = c.id
                 INNER JOIN ruolo_pers_fam rpf ON pc.cod_ruolo_pers_fam = rpf.cod
                 INNER JOIN persone p ON pc.id_pers = p.id
                 WHERE c.id = $id_casa";

        $result = $conn->query($query);

        if ($result->num_rows != 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Persona</th>
                            <th>Nominativo</th>
                            <th>Ruolo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_array()): ?>
                            <tr>
                                <td><?php echo $row['id_pers']; ?></td>
                                <td><?php echo utf8_encode($row['nominativo']); ?></td>
                                <td><?php echo "$row[cod_ruolo_pers_fam] - $row[desc_ruolo]"; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Nessuna casa è presente nel database.</div>
        <?php endif; ?>

        <a href="gest_persone.php?pag=<?php echo $pag; ?>" class="back-link">
            <i class="fa fa-arrow-left"></i> Torna a gestione persone
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal functionality
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("img01");

        // Handle image clicks
        var images = document.getElementsByClassName('modal_image');
        for(var i = 0; i < images.length; i++) {
            images[i].addEventListener('click', function() {
                modal.style.display = "block";
                modalImg.src = this.src;
            });
        }

        // Close modal
        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() { 
            modal.style.display = "none";
        }

        // Close when clicking outside image
        modal.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>