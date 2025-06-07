<?php
/*
*** modifica_persona.php
*** 14/3/2020: A.Carlone:  correzioni varie
*** 2/03/2020  Gobbi Dennis Arneodo Alessandro: inserimento dei dati nelle tabelle storico
*/
$config_path = __DIR__;
$util1 = $config_path .'/../util.php';
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
require_once $util1;
setup();
isLogged("gestore");

// Gestione sicura della variabile di sessione pag_p
$pag = $_SESSION['pag_p']['pag_p'] ?? 1; // Default a 1 se non esiste
// unset($_SESSION['pag_p']); // Rimasto commentato come nel codice originale

// Verifica che id_pers sia stato passato
if (!isset($_POST['id_pers']) || empty($_POST['id_pers'])) {
    die("Errore: ID persona non specificato.");
}
$id_pers = (int)$_POST['id_pers'];
$_SESSION["id_persona_modifica"] = $id_pers;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php stampaIntestazione(); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .btn-submit {
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }
        .btn-submit:hover {
            background-color: #0b5ed7;
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
        h3 {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
   <?php stampaNavbar(); ?>
   <div class="container">
    <?php
    $conn->begin_transaction();
    $query = "SELECT * FROM persone WHERE id=$id_pers FOR UPDATE";
    $result = $conn->query($query);
    
    if (!$result) {
        $msg_err = "Errore select for update";
        echo $conn->error;
    }

    $query = "SELECT p.nominativo, p.data_nascita, p.data_morte, pc.id_casa as id_casa,";
    $query .= " rpf.cod as cod_ruolo, rpf.descrizione as desc_ruolo,matricola_stud as matricola ";
    $query .=  " FROM  persone p  INNER JOIN  pers_casa pc  ON p.id=pc.id_pers ";
    $query .= " INNER JOIN  ruolo_pers_fam rpf  ON pc.cod_ruolo_pers_fam= rpf.cod ";
    $query .= " WHERE  p.id=$id_pers ";

    $result = $conn->query($query);
    $nr = $result->num_rows;
    
    if($nr == 1) {
        $row = $result->fetch_array();
        $id_casa_mod = $row['id_casa'];
        $cod_ruolo_mod = $row['cod_ruolo'];
        $matricola = $row['matricola'];
        $nominativo = utf8_encode($row['nominativo']);
        ?>
        <div class="form-container">
            <h3>Modifica persona: <?php echo htmlspecialchars($nominativo); ?> (id= <?php echo $id_pers; ?>)</h3>
            <form action='modifica_persona.php' name='form' id='form' method='post'>
                <div class="form-group">
                    <label for='nominativo' class="form-label">Nominativo:</label>
                    <input type='text' class="form-control" name='nominativo' value='<?php echo htmlspecialchars($nominativo); ?>' required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for='datan' class="form-label">Data nascita:</label>
                        <input type='date' class="form-control" name='data_nascita' value='<?php echo htmlspecialchars($row['data_nascita']); ?>' required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for='datam' class="form-label">Data morte:</label>
                        <input type='date' class="form-control" name='data_morte' value='<?php echo htmlspecialchars($row['data_morte']); ?>'>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for='casa' class="form-label">Residente nella casa:</label>
                    <select name='id_casa_modifica' class="form-select" required>
                        <?php
                        $query = "SELECT id, nome FROM casa c";
                        $result = $conn->query($query);
                        $nr = $result->num_rows;
                        for($i = 0; $i < $nr; $i++) {
                            $row = $result->fetch_array();
                            $selected = ($id_casa_mod == $row["id"]) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($row["id"])."' $selected>".htmlspecialchars($row["nome"])."</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for='ruolo' class="form-label">Ruolo nella famiglia:</label>
                    <select name='id_ruolo_modifica' class="form-select" required>
                        <?php
                        $query = "SELECT distinct cod, descrizione FROM ruolo_pers_fam";
                        $result = $conn->query($query);
                        $nr = $result->num_rows;
                        for($i = 0; $i < $nr; $i++) {
                            $row = $result->fetch_array();
                            $selected = ($cod_ruolo_mod == $row["cod"]) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($row["cod"])."' $selected>".htmlspecialchars($row["descrizione"])."</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <?php
                if($matricola != null) {
                    $query = "SELECT data_inizio_val,data_fine_val FROM studenti where matricola='".$conn->real_escape_string($matricola)."'";
                    $result = $conn->query($query);
                    $row = $result->fetch_array();
                } else {
                    $matricola = "";
                }
                $inizio_matricola = isset($row["data_inizio_val"]) ? $row["data_inizio_val"] : "";
                $fine_matricola = isset($row["data_fine_val"]) ? $row["data_fine_val"] : "";
                ?>
                
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for='matricola' class="form-label">Matricola scuola:</label>
                        <input type='text' class="form-control" name='matricola' placeholder='Modifica matricola se è uno studente' value='<?php echo htmlspecialchars($matricola); ?>'>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for='inizio_matricola' class="form-label">Data inizio scuola:</label>
                        <input type='date' class="form-control" name='inizio_matricola' value='<?php echo htmlspecialchars($inizio_matricola); ?>'>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for='fine_matricola' class="form-label">Data fine scuola:</label>
                        <input type='date' class="form-control" name='fine_matricola' value='<?php echo htmlspecialchars($fine_matricola); ?>'>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type='submit' class='btn btn-primary btn-submit'>Modifica</button>
                </div>
            </form>
            <a href='gest_persone.php?pag=<?php echo $pag; ?>' class='back-link'>Torna a gestione persone</a>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-danger'>Mancano le specifiche per poterla modificare</div>";
    }
    ?>
   </div>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
      // Remove any existing window.onclick handlers that might conflict
        window.onclick = null;
   </script>
</body>
</html>