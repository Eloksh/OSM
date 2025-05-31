<?php
/*
*** del_moranca.php
*** cancellazione moranca 
*** Viene attivato dal form di delete_moranca.php
*** 01/03/2020: Gobbi, Arneodo:  Correzione problema durante l'eliminazione delle moran�e
*** 20/02/2020: A. Carlone
*/
$config_path = __DIR__;
$util1 = $config_path .'/../util.php';
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
require_once $util1;
setup();
isLogged("gestore");
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .confirmation-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .confirmation-title {
            color: #dc3545;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .confirmation-message {
            font-size: 1.1rem;
            margin-bottom: 25px;
        }
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        .btn-no {
            background-color: #6c757d;
            color: white;
            padding: 8px 25px;
        }
        .btn-no:hover {
            background-color: #5a6268;
            color: white;
        }
        .btn-si {
            background-color: #dc3545;
            color: white;
            padding: 8px 25px;
        }
        .btn-si:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
</head>
<body>
    <?php stampaIntestazione(); ?>
    <?php stampaNavbar(); ?>
<?php 
$id_moranca = $_POST['id_moranca'];

$query = "SELECT ";
$query .= " m.id, m.nome as nome_moranca, m.id_mor_zona, m.cod_zona,z.nome as desc_zona";
$query .= " FROM morance m, zone z";
$query .= " WHERE m.cod_zona =  z.cod";
$query .= " AND m.id =  $id_moranca";
 
$result = $conn->query($query);
if (!$result )
   throw new Exception($conn->error);

$row = $result->fetch_array();
$desc_zona = $row['desc_zona'];
$nome_moranca = $row['nome_moranca'];
?>
    <div class="container">
        <div class="confirmation-container">
            <?php
           echo "<h3 class='confirmation-title'>CANCELLAZIONE Moranca</h3>";
           echo "<br>CANCELLAZIONE MORANCA: id: $id_moranca - moranca: $nome_moranca - zona:$desc_zona <br><br>";
            ?>
            
            <form method='POST' action='delete_moranca.php'>
                <div class="btn-group">
                    <input type='submit' class='btn btn-no' name='no' value='No'>
                    <input type='submit' class='btn btn-si' name='si' value='Si'>
                    <input type='hidden' name='id_mora' value='<?php echo $id_moranca; ?>'>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>