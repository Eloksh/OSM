<?php
/*
*** Gest_persone.php: Gestione delle persone (Versione Responsive)
*** Attivato da menu principale alla voce "persone"
*** Input:
*** se  POST(id_casa) valorizzato -> (arriva da gestione_case.php: 
***                si vuole l'elenco delle persone di una casa specifica
*** se  POST(cod_zona) valorizzato -> (arriva da qui per la scelta della zona)
***                si vuole l'elenco delle persone di una casa specifica
*** Output. Può richiamare:
*** mod_persona.php  (caso di modifica persona)
*** del_persona.php  (caso di cancellazione persona)
*** mostra_casa.php  (caso di motra dati della casa della persona)
*** vis_persona_sto.php  (caso di visualizzazione storico della persona)
***  9/4/2020: A.Carlone: visualizzazione deceduti
*** 15/3/2020: A.Carlone: migliorata gestione zone e ordinamento su id e nome moranca
*** 2/2/2020: A. Carlone: prima implementazione
*** 05/06/2025: ELOKSH MODIFICA CSSs
*/
$config_path = __DIR__;
$util1 = $config_path .'/../util.php';
require_once $util1;
setup();
isLogged("gestore");
unsetPag(basename(__FILE__)); 
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Persone</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CSS Custom -->
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/gest-persone.css">
</head>

<?php
$util2 = $config_path .'/../db/db_conn.php';
require_once $util2;
?>

<?php stampaIntestazione(); ?>

<body>
    <?php stampaNavbar(); ?>

    <div class="container-fluid mt-4">
        <?php
        // Gestione variabili di sessione e POST
        if (isset($_SESSION['cod_zona']))
            $cod_zona = $_SESSION['cod_zona'];
        else
            $cod_zona = "tutte"; 

        if (isset($_SESSION['id_casa']))
            $id_casa = $_SESSION['id_casa'];
        else
            $id_casa = "tutte"; 

        if (isset($_SESSION['decessi'])) 
        {
            $_SESSION['old_decessi'] =  $_SESSION['decessi'];
            $decessi = $_SESSION['old_decessi'];
        }

        if (isset($_POST['decessi']))
        {
            $decessi = $_POST['decessi'];
            $_SESSION['decessi'] = $decessi;
        }  
        else 
        {
            if( isset($_SESSION['decessi']))		
                $decessi =  $_SESSION['decessi'];
            else
                $decessi = 'tutti'; 
        }

        if (isset($_SESSION['ord_p']))
            $ord = $_SESSION['ord_p'];
        else
            $ord = "ASC";

        if (isset($_SESSION['campo_p']))
            $campo = $_SESSION['campo_p'];
        else
            $campo = "nominativo";

        if(isset($_GET['pag']))
            $pag= $_GET['pag'];
        else
            $pag= 0;
        ?>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-center page-title">
                    <i class='fa fa-users'></i> Elenco Persone
                </h2>
            </div>
        </div>

        <!-- Action Links -->
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <a href='vis_sto_tot_persone.php' class="btn btn-info btn-sm">
                    <i class="fa fa-history"></i> Storia delle persone
                </a>
                <a href='export_persone.php' class="btn btn-success btn-sm ms-2">
                    <i class="fa fa-file-excel"></i> Export Excel
                </a>
            </div>
            <div class="col-md-6 mb-2 text-md-end">
                <a href='ins_persona.php' class="btn btn-primary">
                    <i class='fa fa-plus-circle'></i> Nuova Persona
                </a>
            </div>
        </div>

        <!-- Search Box -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="search-box">
                            <form action='gest_persone.php' method='POST' class="row g-3">
                                <div class="col-md-8">
                                    <input type='text' class="form-control" autocomplete='off' name='nome' placeholder='Cerca per nominativo...'>
                                    <div class="result"></div>
                                </div>
                                <div class="col-md-4">
                                    <input type='submit' name='ricerca' class='btn btn-primary w-100' value='Cerca'>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $x_pag = 10;
        $ricerca = false;
        if ((isset($_POST['ricerca'])) && ($_POST['nome'] != ""))
        {
            $pag = get_first_pag($conn, $_POST['nome'],$id_casa, $decessi, $cod_zona, $ord, $campo); 
            $ricerca = true;
        }

        // Gestione casa e zona
        if (isset($_POST['id_casa']))
        {
            $id_casa = $_POST['id_casa']; 
            $_SESSION['id_casa'] = $id_casa;
        }  
        else 
            $_SESSION['id_casa'] = 'tutte';

        if( isset($_SESSION['id_casa']) && ($_SESSION['id_casa'] != 'tutte'))		
            $id_casa =  $_SESSION['id_casa']; 

        if (isset($_POST['cod_zona']))
        {
            $cod_zona = $_POST['cod_zona']; 
            $_SESSION['cod_zona'] = $cod_zona;
        }  
        else 
        {
            if( isset($_SESSION['cod_zona']) && ($_SESSION['cod_zona'] != 'tutte'))		
                $cod_zona =  $_SESSION['cod_zona'];
        } 

        if (!$ricerca)
            $pag=Paginazione($pag, "pag_p");

        // Ensure pag is at least 1
        if ($pag < 1) {
            $pag = 1;
        }

        // Query count
        $query2 = "SELECT count(p.id) as cont FROM persone p";
        $query2 .= " inner join pers_casa pc on pc.id_pers = p.id ";
        $query2 .= " inner join casa c       on pc.id_casa = c.id";
        $query2 .= " inner join morance m    on c.id_moranca = m.id";
        $query2 .= " inner join ruolo_pers_fam rpf ON  pc.cod_ruolo_pers_fam = rpf.cod ";

        if (isset($cod_zona) && ($cod_zona != 'tutte'))
            $query2 .= " inner join zone z on m.cod_zona = z.cod";

        $query2 .= " WHERE p.data_fine_val IS  NULL";

        if (isset($id_casa) && ($id_casa != 'tutte'))
            $query2 .= " AND c.id = $id_casa"; 

        if (isset($cod_zona) && ($cod_zona != 'tutte'))
            $query2 .= " AND z.cod = '$cod_zona'";

        if (isset($decessi) && ($decessi == 'si'))
            $query2 .= " AND p.data_morte IS NOT NULL";
        if (isset($decessi) && ($decessi == 'no'))
            $query2 .= " AND p.data_morte IS  NULL";

        $result = $conn->query($query2);
        $row = $result->fetch_array();
        $all_rows= $row['cont'];
        $all_pages = ceil($all_rows / $x_pag);

        if (!$ricerca) {
            $first = ($pag - 1) * $x_pag;
        } else {
            $first = ($pag) * $x_pag;
        }

        // Ensure first is never negative
        if ($first < 0) {
            $first = 0;
            $pag = 1;
        }

        // Also ensure pag doesn't exceed total pages
        if ($pag > $all_pages && $all_pages > 0) {
            $pag = $all_pages;
            $first = ($pag - 1) * $x_pag;
        }
        ?>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Filtro Zona</h6>
                        <form action='gest_persone.php' method='POST' class="row g-2">
                            <div class="col-8">
                                <select name='cod_zona' class="form-select">
                                    <option value='tutte'>Tutte le zone</option>
                                    <?php
                                    $result = $conn->query("SELECT * FROM zone");
                                    while($row = $result->fetch_array()) {
                                        $selected = (isset($cod_zona) && $cod_zona == $row["COD"]) ? "selected" : "";
                                        echo "<option value='".$row["COD"]."' $selected>". $row["NOME"]." </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <input type='submit' class='btn btn-outline-primary w-100' value='Filtra'>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Filtro Stato</h6>
                        <form action='gest_persone.php' method='POST' class="row g-2">
                            <div class="col-8">
                                <select name='decessi' class="form-select">
                                    <option value='tutti' <?php echo (isset($decessi) && $decessi == 'tutti') ? 'selected' : ''; ?>>Tutti</option>
                                    <option value='no' <?php echo (isset($decessi) && $decessi == 'no') ? 'selected' : ''; ?>>Viventi</option>
                                    <option value='si' <?php echo (isset($decessi) && $decessi == 'si') ? 'selected' : ''; ?>>Deceduti</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <input type='submit' class='btn btn-outline-primary w-100' value='Filtra'>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Gestione ordinamento
        if (isset($_SESSION['campo_p']))
            $campo = $_SESSION['campo_p'];
        else 
            $campo = "nominativo";

        if (isset($_SESSION['ord_p']))
            $ord = $_SESSION['ord_p'];
        else 
            $ord = "ASC"; 

        if (isset($_POST['ord_id']) || isset($_POST['ord_nominativo']))
        {
            if (isset($_POST['ord_id']))
                $campo = 'id';
            else 
                $campo = 'nominativo';

            if ($ord == 'ASC')
                $ord = "DESC";
            else
                $ord = "ASC";
            $first = 0;
            $pag = 1;
        }

        $_SESSION['campo_p'] = $campo;
        $_SESSION['ord_p'] = $ord;

        $result->free();

        // Query principale
        $query = "SELECT ";
        $query .= " p.id, p.nominativo, p.sesso, p.data_nascita, p.data_morte,";
        $query .= " c.id as id_casa, c.id_moranca,c.nome nome_casa, m.nome nome_moranca,";
        $query .= " m.cod_zona, z.nome zona , c.id_casa_moranca, c.id_osm, ";
        $query .= " pc.cod_ruolo_pers_fam, rpf.descrizione,";
        $query .= " p.data_inizio_val, p.data_fine_val,s.matricola ";
        $query .= " FROM persone p";
        $query .= " INNER JOIN pers_casa pc ON  pc.id_pers = p.id";
        $query .= " INNER JOIN casa c ON  pc.id_casa = c.id";
        $query .= " INNER JOIN morance m ON  c.id_moranca = m.id";
        $query .= " INNER JOIN zone z ON  m.cod_zona = z.cod";
        $query .= " INNER JOIN ruolo_pers_fam rpf ON  pc.cod_ruolo_pers_fam = rpf.cod ";
        $query .= " LEFT JOIN studenti s ON  s.matricola = p.matricola_stud";
        $query .= " WHERE p.data_fine_val IS  NULL";
        
        if (isset($cod_zona) && ($cod_zona !='tutte'))
            $query .= " AND m.cod_zona = '$cod_zona'"; 
        if (isset($id_casa)&& ($id_casa !='tutte'))
            $query .= " AND id_casa = $id_casa";
        if (isset($decessi) && ($decessi == 'si'))
            $query .= " AND p.data_morte IS NOT NULL";
        if (isset($decessi) && ($decessi == 'no'))
            $query .= " AND p.data_morte IS  NULL";
            
        $query .= " ORDER BY $campo " . $ord ;
        $query .= " LIMIT $first, $x_pag";

        $result = $conn->query($query);
        $nr = $result->num_rows;

        $vis_decessi = true;       
        if (isset($decessi))
        {
            if (($decessi == 'si') || ($decessi == 'tutti'))
                $vis_decessi = true;
            else 
                $vis_decessi = false;
        }

        if ($nr != 0) {
        ?>

        <!-- Results Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            <form method='post' action='gest_persone.php' class="d-inline">
                                                Nominativo 
                                                <button class='btn btn-sm btn-outline-light ms-1' name='ord_nominativo' value='nominativo' type='submit'>
                                                    <i class="<?php echo ($ord == "ASC") ? "fa fa-arrow-down" : "fa fa-arrow-up"; ?>"></i>
                                                </button>
                                            </form>
                                        </th>
                                        <th>
                                            <form method='post' action='gest_persone.php' class="d-inline">
                                                ID 
                                                <button class='btn btn-sm btn-outline-light ms-1' name='ord_id' value='id' type='submit'>
                                                    <i class="<?php echo ($ord == "ASC") ? "fa fa-arrow-down" : "fa fa-arrow-up"; ?>"></i>
                                                </button>
                                            </form>
                                        </th>
                                        <th class="d-none d-md-table-cell">Sesso</th>
                                        <th class="d-none d-lg-table-cell">Data Nascita</th>
                                        <?php if ($vis_decessi) echo "<th class='d-none d-lg-table-cell'>Data Decesso</th>"; ?>
                                        <th class="d-none d-md-table-cell">Età</th>
                                        <th class="d-none d-xl-table-cell">Ruolo</th>
                                        <th class="d-none d-xl-table-cell">Matricola</th>
                                        <th class="d-none d-lg-table-cell">Casa</th>
                                        <th class="d-none d-xl-table-cell">Morança</th>
                                        <th class="d-none d-xl-table-cell">Zona</th>
                                        <th class="d-none d-xl-table-cell">Mappa</th>
                                        <th class="d-none d-xl-table-cell">Data Inizio</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function invertiData($data) {
                                        if($data == "0000-00-00" || empty($data)) {
                                            return $data;
                                        }
                                        $parti = explode("-", $data);
                                        if(count($parti) == 3) {
                                            return $parti[2] . "-" . $parti[1] . "-" . $parti[0];
                                        }
                                        return $data;
                                    }

                                    while ($row = $result->fetch_array()) {
                                        $mystr = utf8_encode($row['nominativo']);
                                        $data_nascita = invertiData($row['data_nascita']);
                                        $data_morte = invertiData($row['data_morte']);
                                        $data_inizio_val = invertiData($row['data_inizio_val']);
                                        
                                        echo "<tr>";
                                        echo "<td class='my-td-class'><strong>$mystr</strong></td>";
                                        echo "<td><span class='badge bg-secondary'>$row[id]</span></td>";
                                        echo "<td class='d-none d-md-table-cell'>$row[sesso]</td>";
                                        echo "<td class='d-none d-lg-table-cell'>$data_nascita</td>";
                                        
                                        if ($vis_decessi)
                                            echo "<td class='d-none d-lg-table-cell'>".$data_morte."</td>";

                                        // Calcolo età
                                        if (($row['data_morte'] != "") && ($row['data_morte'] != "0000-00-00"))
                                            $eta = date_diff(date_create($row['data_nascita']), date_create($row['data_morte']))->y;
                                        else
                                            $eta = date_diff(date_create($row['data_nascita']), date_create('today'))->y;
                                        
                                        echo "<td class='d-none d-md-table-cell'>$eta anni</td>";
                                        echo "<td class='d-none d-xl-table-cell'><small>$row[descrizione] ($row[cod_ruolo_pers_fam])</small></td>";
                                        echo "<td class='d-none d-xl-table-cell'>$row[matricola]</td>";
                                        echo "<td class='d-none d-lg-table-cell'>$row[nome_casa]</td>";
                                        
                                        $mystr = utf8_encode($row['nome_moranca']);
                                        echo "<td class='d-none d-xl-table-cell'><small>$mystr</small></td>";
                                        echo "<td class='d-none d-xl-table-cell'>$row[zona]</td>";
                                        
                                        $osm_link = "https://www.openstreetmap.org/way/$row[id_osm]";
                                        if ($row['id_osm'] != null && $row['id_osm'] != "0") {
                                            echo "<td class='d-none d-xl-table-cell'><a href='$osm_link' target='_blank' class='text-decoration-none'><i class='fa fa-map-marker-alt text-danger'></i></a></td>";
                                        } else {
                                            echo "<td class='d-none d-xl-table-cell'>-</td>";
                                        }
                                        
                                        echo "<td class='d-none d-xl-table-cell'><small>$data_inizio_val</small></td>";
                                        
                                        // Azioni
                                        echo "<td>";
                                        echo "<div class='btn-group' role='group'>";
                                        
                                        echo "<form method='post' action='mod_persona.php' class='d-inline'>";
                                        echo "<button class='btn btn-sm btn-warning' name='id_pers' value='$row[id]' type='submit' title='Modifica'>";
                                        echo "<i class='fa fa-edit'></i></button></form>";
                                        
                                        echo "<form method='post' action='del_persona.php' class='d-inline delete-form'>";
                                        echo "<input type='hidden' name='id_pers' value='$row[id]'>";
                                        echo "<button class='btn btn-sm btn-danger delete-btn' type='button' title='Elimina' data-person-name='".htmlspecialchars($mystr, ENT_QUOTES)."'>";
                                        echo "<i class='fa fa-trash'></i></button></form>";
                                        
                                        echo "<form method='post' action='mostra_casa.php' class='d-inline'>";
                                        echo "<button class='btn btn-sm btn-info' name='id_persona' value='$row[id]' type='submit' title='Casa'>";
                                        echo "<i class='fa fa-home'></i></button></form>";
                                        
                                        echo "<form method='post' action='vis_persona_sto.php' class='d-inline'>";
                                        echo "<button class='btn btn-sm btn-secondary' name='id_persona' value='$row[id]' type='submit' title='Storico'>";
                                        echo "<i class='fa fa-history'></i></button></form>";
                                        
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php } ?>

        <!-- Results Summary -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> 
                    Numero abitanti risultanti da questa ricerca: <strong><?php echo $all_rows; ?></strong>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                <nav aria-label="Paginazione risultati">
                    <ul class="pagination justify-content-center">
                        <?php
                        $vis_pag = $config_path .'/../vis_pag.php';
                        require $vis_pag;
                        ?>
                    </ul>
                </nav>
            </div>
        </div>

    </div>

    <!-- Scripts moved to bottom -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function(){
            // Enhanced search functionality with error handling
            $('.search-box input[type="text"]').on("keyup input", function(){
                var inputVal = $(this).val();
                var resultDropdown = $(this).siblings(".result");
                
                if(inputVal.length > 1){
                    $.get("cerca_persona.php", {term: inputVal})
                        .done(function(data){
                            if(resultDropdown.length > 0){
                                resultDropdown.html(data);
                            }
                        })
                        .fail(function(xhr, status, error){
                            console.log("Search error: " + error);
                            if(resultDropdown.length > 0){
                                resultDropdown.empty();
                            }
                        });
                } else {
                    if(resultDropdown.length > 0){
                        resultDropdown.empty();
                    }
                }
            });

            // Click handler for search results
            $(document).on("click", ".result p", function(){
                var searchBox = $(this).parents(".search-box");
                var input = searchBox.find('input[type="text"]');
                var result = $(this).parent(".result");
                
                if(input.length > 0){
                    input.val($(this).text());
                }
                if(result.length > 0){
                    result.empty();
                }
            });

            // Handle delete button clicks
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                var personName = $(this).data('person-name') || 'questa persona';
                var form = $(this).closest('.delete-form');
                
                if(confirm("Sei sicuro di voler eliminare " + personName + "?")) {
                    form.submit();
                }
            });

            // Close search results when clicking outside
            $(document).on('click', function(e) {
                if(!$(e.target).closest('.search-box').length) {
                    $('.search-box .result').empty();
                }
            });
        });

        // Remove any existing window.onclick handlers that might conflict
        window.onclick = null;
    </script>

    <?php
    $result->free();
    $conn->close();

    function get_first_pag($conn, $nominativo, $id_casa, $decessi, $cod_zona, $ord, $campo_ord)
    { 
        $nominativo = utf8_decode($nominativo);
        $query = "SELECT id id_p FROM persone  WHERE nominativo = '{$nominativo}'";
        $result = $conn->query($query);
        $row = $result->fetch_array();
        $id = $row['id_p'];
        $result->free();

        $query = "SELECT ";
        $query .= " p.id, p.nominativo, p.sesso, p.data_nascita, p.data_morte,";
        $query .= " c.id as id_casa, c.id_moranca,c.nome nome_casa, m.nome nome_moranca,";
        $query .= " m.cod_zona,  c.id_casa_moranca, c.id_osm, ";
        $query .= " pc.cod_ruolo_pers_fam, rpf.descrizione,";
        $query .= " p.data_inizio_val, p.data_fine_val ";
        $query .= " FROM persone p";
        $query .= " INNER JOIN pers_casa pc ON  pc.id_pers = p.id";
        $query .= " INNER JOIN casa c ON  pc.id_casa = c.id";
        $query .= " INNER JOIN morance m ON  c.id_moranca = m.id";
        $query .= " INNER JOIN ruolo_pers_fam rpf ON  pc.cod_ruolo_pers_fam = rpf.cod ";
        $query .= " WHERE p.data_fine_val IS  NULL";
        
        if (isset($cod_zona) && ($cod_zona !='tutte'))
            $query .= " AND m.cod_zona = '$cod_zona'"; 
        if (isset($id_casa)&& ($id_casa !='tutte'))
            $query .= " AND id_casa = $id_casa";
        if (isset($decessi) && ($decessi == 'si'))
            $query .= " AND p.data_morte IS NOT NULL";
        if (isset($decessi) && ($decessi == 'no'))
            $query .= " AND p.data_morte IS  NULL";

        if ($campo_ord == "nominativo")
            $campo_ord = "p.nominativo";
        else
            $campo_ord = "p.id";

        if ($campo_ord == "p.nominativo")
        {
            if ($ord == "ASC")
                $query .= " AND $campo_ord  <= '".$nominativo."'";
            else
                $query .= " AND $campo_ord >= '".$nominativo."'";
        }
        else
        {
            if ($ord == "ASC")
                $query .= " AND $campo_ord  <= ".$id;
            else
                $query .= " AND  $campo_ord >= ".$id;
        }
        $query .= " ORDER BY $campo_ord " . $ord ;

        $result = $conn->query($query);
        $cont=$result->num_rows;
        $result->free();

        $x_pag = 10;
        $resto = $cont%$x_pag;

        if ($resto == 0)
            $pag = intval(abs($cont/$x_pag)) - 1;
        else
            $pag = intval(abs($cont/$x_pag));
        
        // Ensure pag is never negative
        if ($pag < 0) {
            $pag = 0;
        }
            
        return $pag;
    }
    ?>

</body>
</html>