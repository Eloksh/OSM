<?php
$config_path = __DIR__;
$util1 = "../util.php";
$util2 = "../db/db_conn.php";
require_once $util2;
require_once $util1;
setup();
isLogged("gestore");

// Safely get the pagination value
$pag = $_SESSION['pag_c']['pag_c'] ?? 1; // Default to page 1 if not set
if (isset($_SESSION['pag_c'])) {
    unset($_SESSION['pag_c']);
}

// Check if id_casa is set in POST
if (!isset($_POST['id_casa']) || empty($_POST['id_casa'])) {
    die("Errore: ID casa non specificato.");
}
$id_casa = (int)$_POST['id_casa'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php stampaIntestazione(); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
		a.btn.btn-secondary{
			background-color: rgb(125, 148, 148);
		}

        body {
            padding: 20px;
            background-color: #f8f9fa;
        }

        div.header-info {
            background-color: rgb(255, 255, 255);
            padding: 15px;
            border-radius: 5px;
            border: black solid 0.1px;
        }
    
        .header-info {
            background-color:rgb(232, 232, 232);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
			border: black solid 0.1px;
            background-color: white;
        }
        .table th {
            background-color: #f1f1f1;
            white-space: nowrap;
            position: sticky;
            top: 0;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 2px;
        }
        .actions-cell {
            white-space: nowrap;
        }
        @media (max-width: 768px) {
            .table-responsive {
                width: 100%;
                margin-bottom: 15px;
                overflow-y: hidden;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                border: 1px solid #ddd;
            }
            .table th, .table td {
                padding: 0.5rem;
                font-size: 0.9rem;
            }
            .table thead th {
                font-size: 0.85rem;
            }
            .btn-action {
                display: inline-block;
                margin: 2px;
                font-size: 0.8rem;
                padding: 0.2rem 0.4rem;
            }
        }
        @media (max-width: 576px) {
            .table th, .table td {
                padding: 0.3rem;
                font-size: 0.8rem;
            }
            .btn-action {
                font-size: 0.75rem;
            }
            .hide-on-mobile {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php stampaNavbar(); ?>
    
    <?php
    $util2 = $config_path . '/../db/db_conn.php';
    require_once $util2;

    // Query per ottenere i dati della casa
    $query = "SELECT c.id_moranca, c.nome as nome_casa, m.id as id_moranca, 
              m.nome as nome_moranca, m.cod_zona as zona 
              FROM casa c 
              INNER JOIN morance m ON c.id_moranca = m.id
              WHERE c.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_casa);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("Casa non trovata.");
    }

    $row = $result->fetch_array();
    $nome_casa = utf8_encode($row['nome_casa']);
    $nome_moranca = utf8_encode($row['nome_moranca']);
    $id_moranca = $row['id_moranca'];
    $zona = $row['zona'];
    $result->free();

    // Query per ottenere gli abitanti
    $query = "SELECT p.id, p.nominativo, p.sesso, p.data_nascita,
              pc.cod_ruolo_pers_fam, rpf.descrizione
              FROM persone p
              INNER JOIN pers_casa pc ON pc.id_pers = p.id
              INNER JOIN casa c ON pc.id_casa = c.id
              INNER JOIN morance m ON c.id_moranca = m.id
              INNER JOIN ruolo_pers_fam rpf ON pc.cod_ruolo_pers_fam = rpf.cod
              WHERE p.data_fine_val IS NULL   
              AND pc.data_fine_val IS NULL
              AND c.data_fine_val IS NULL
              AND m.data_fine_val IS NULL
              AND pc.id_casa = ?
              ORDER BY nominativo ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_casa);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
 <h3 class="mb-3" style="text-align: center;margin-top: 20px">Elenco abitanti della casa</h3>
    <div class="container">
        <div class="header-info">
            <p class="mb-1"><strong>Casa:</strong> <?php echo htmlspecialchars($nome_casa); ?> (ID: <?php echo $id_casa; ?>)</p>
            <p class="mb-1"><strong>Moranca:</strong> <?php echo htmlspecialchars($nome_moranca); ?> (ID: <?php echo $id_moranca; ?>)</p>
            <p><strong>Zona:</strong> <?php echo htmlspecialchars($zona); ?></p>
        </div>

        <?php if ($result->num_rows > 0) : ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>  
                        <th>Nominativo</th>
                        <th>Sesso</th>		
                        <th>Data nascita</th>
                        <th class="hide-on-mobile">Cod ruolo</th>
                        <th class="hide-on-mobile">Descrizione</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_array()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars(utf8_encode($row['nominativo'])); ?></td>
                        <td><?php echo htmlspecialchars($row['sesso']); ?></td>
                        <td><?php echo htmlspecialchars($row['data_nascita']); ?></td>
                        <td class="hide-on-mobile"><?php echo htmlspecialchars($row['cod_ruolo_pers_fam']); ?></td>
                        <td class="hide-on-mobile"><?php echo htmlspecialchars($row['descrizione']); ?></td>
                        <td class="actions-cell">
                            <div class="d-flex flex-wrap">
                                <form method="post" action="../persone/mod_persona.php" class="me-1 mb-1">
                                    <button class="btn btn-primary btn-sm btn-action" name="id_pers" value="<?php echo $row['id']; ?>" type="submit" title="Modifica">
                                        <i class="fa fa-pencil"></i> <span class="d-none d-sm-inline">Modifica</span>
                                    </button>
                                </form>
                                <form method="post" action="../persone/del_persona.php" class="mb-1">
                                    <button class="btn btn-danger btn-sm btn-action" name="id_pers" value="<?php echo $row['id']; ?>" type="submit" title="Elimina">
                                        <i class="fa fa-trash"></i> <span class="d-none d-sm-inline">Elimina</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
            <div class="alert alert-info">Nessuna persona è presente.</div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="gest_case.php?pag=<?php echo $pag; ?>" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Torna a gestione case
            </a>
        </div>
    </div>

    <?php
    $result->free();
    $stmt->close();
    $conn->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>