<?php
/*
*** del_casa.php
*** cancellazione casa 
*** Viene attivato da gest_case.php
*** attiva delete_casa.php
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

    <div class="container">
        <div class="confirmation-container">
            <?php
            $id_casa = $_POST['id_casa'];
            echo "<h3 class='confirmation-title'>CANCELLAZIONE Casa</h3>";
            echo "<p class='confirmation-message'>Identificativo: <strong>$id_casa</strong><br><br>La vuole davvero cancellare?</p>";
            ?>
            
            <form method='POST' action='delete_casa.php'>
                <div class="btn-group">
                    <input type='submit' class='btn btn-no' name='no' value='No'>
                    <input type='submit' class='btn btn-si' name='si' value='Si'>
                    <input type='hidden' name='id_pers' value='<?php echo $id_casa; ?>'>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>