<?php
/******************* Util.php *************************/
//Data ultima modifica:27/02/2020
//Descrizione:Implementazione della gestione multilingue attraverso un file .json Autore:Gobbi Dennis
//Descrizione:Gestione degli utenti Autore:Ferraiuolo Pasquale
//25/03/2020: Ferraiuolo: Aggiunta menu a tendina e nome utente nella videata
//Aggiornamento: Navbar responsive con Bootstrap
?>
<?php

/* definizione di costanti */
define("OK", 0);
define("KO", -1);

function stampaNavbar()
{
?>
<nav class="navbar navbar-expand-lg bg-primary fixed-top modern-navbar">
    <div class="container-fluid">
        <!-- Brand/Logo -->
        <a class="navbar-brand fw-bold brand-logo" href="/OSM/Anagrafe/index.php">
            <div class="brand-text">
                <span class="brand-main">Nague</span>
                <span class="brand-sub">AnagrafeWEB</span>
            </div>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (login()) { ?>
                <!-- Menu Items for Logged Users -->
                <ul class="navbar-nav me-auto">
                    <?php if($_SESSION['tipo']!="utente") { ?>
                        <li class="nav-item">
                            <a class="nav-link nav-item-custom" href="/OSM/Anagrafe/morance/gest_morance.php">
                                <div class="nav-content">
                                    <img src="/OSM/Anagrafe/img/moranca4.png" width="30" height="24" alt="Morancas" class="nav-icon">
                                    <span class="nav-text">Morançãs</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-item-custom" href="/OSM/Anagrafe/case/gest_case.php">
                                <div class="nav-content">
                                    <img src="/OSM/Anagrafe/img/casa4.png" width="30" height="24" alt="Case" class="nav-icon">
                                    <span class="nav-text">Case</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-item-custom" href="/OSM/Anagrafe/persone/gest_persone.php">
                                <div class="nav-content">
                                    <i class="fa fa-users nav-icon"></i>
                                    <span class="nav-text">Persone</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-item-custom" href="/OSM/Anagrafe/OSM/db2geojson.php" target="mapcase">
                                <div class="nav-content">
                                    <i class="fa fa-map-marker nav-icon"></i>
                                    <span class="nav-text">Mappa Case</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-item-custom" href="https://www.openstreetmap.org/search?query=nague#map=17/11.95270/-15.48564" target="osm">
                                <div class="nav-content">
                                    <i class="fa fa-globe nav-icon"></i>
                                    <span class="nav-text">OSM</span>
                                </div>
                            </a>
                        </li>
                    <?php } ?>
                    
                    <li class="nav-item">
                        <a class="nav-link nav-item-custom" href="/OSM/Anagrafe/stat/statistiche.php">
                            <div class="nav-content">
                                <i class="fa fa-pie-chart nav-icon"></i>
                                <span class="nav-text">Statistiche</span>
                            </div>
                        </a>
                    </li>
                    
                    <?php if($_SESSION['tipo']=="admin") { ?>
                        <li class="nav-item">
                            <a class="nav-link nav-item-custom" href="/OSM/Anagrafe/utenti/gestione_utenti.php">
                                <div class="nav-content">
                                    <i class="fa fa-user nav-icon"></i>
                                    <span class="nav-text">Utenti</span>
                                </div>
                            </a>
                        </li>
                    <?php } ?>
                </ul>

                <!-- User Menu -->
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user-circle me-1"></i>
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['nome']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end modern-dropdown" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="/OSM/Anagrafe/utenti/area_personale.php">
                                    <i class="fa fa-user me-2"></i>Area Personale
                                </a>
                            </li>
                            <?php if($_SESSION['tipo']=="admin") { ?>
                            <li>
                                <a class="dropdown-item" href="/OSM/Anagrafe/utenti/gestione_utenti.php">
                                    <i class="fa fa-users-cog me-2"></i>Gestione Utenti
                                </a>
                            </li>
                            <?php } ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/OSM/Anagrafe/logout.php" 
                                   onclick="return confirm('Sei sicuro di voler uscire?');">
                                    <i class="fa fa-sign-out me-2"></i>Esci
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            <?php } else { ?>
                <!-- Menu Items for Non-Logged Users -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-item-custom" href="/OSM/Anagrafe/info/chisiamo.php">
                            <div class="nav-content">
                                <i class="fa fa-info-circle nav-icon"></i>
                                <span class="nav-text">Chi Siamo</span>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-item-custom" href="/OSM/Anagrafe/info/progetto.php">
                            <div class="nav-content">
                                <i class="fa fa-project-diagram nav-icon"></i>
                                <span class="nav-text">Il Progetto</span>
                            </div>
                        </a>
                    </li>
                </ul>
                
                <!-- Login Button -->
                <div class="navbar-nav">
                    <a class="nav-link login-btn" href="/OSM/Anagrafe/login.php">
                        <i class="fa fa-sign-in me-1"></i>
                        <span>Accedi</span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div class="navbar-spacer"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced dropdown functionality
    const userDropdown = document.getElementById('userDropdown');
    if (userDropdown) {
        const dropdownMenu = userDropdown.nextElementSibling;
        
        // Ensure dropdown toggle works properly
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = dropdownMenu.classList.contains('show');
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
            
            // Toggle current dropdown
            if (!isOpen) {
                dropdownMenu.classList.add('show');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('show');
            }
        });
        
        // Handle dropdown item clicks
        dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });
        });
    }
    
    // Add active class to current page
    const currentPath = window.location.pathname;
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
});
</script>

<?php
}

// Funzione helper per evidenziare la pagina corrente
function isCurrentPage($page) {
    return (basename($_SERVER['PHP_SELF']) == $page) ? 'active' : '';
}
?>

<script>
    function myFx(){//Funzione per far comparire il dropdown menù 
        var show=document.getElementById("dropMenu").style.display;
        console.log(show);
        if(show=="none" || show=="")document.getElementById("dropMenu").style.display="inline";
        if(show=="inline" )document.getElementById("dropMenu").style.display="none";
    }
    window.onclick = function(){
        if(!event.target.matches(".globe")){
            document.getElementById("dropMenu").style.display="none";
            console.log("Clickato fuori dall'icona globo");
        }
    }
</script>

<script>

function tooltip(event)	// gestione tooltip
{
  document.getElementById("error").style.visibility="visible";
  if(event.type=="mouseover")
   {
    document.getElementById("error").style.visibility="visible";
   }
  else if(event.type=="mouseout")
	  {
        document.getElementById("error").style.visibility="hidden";
      }
 }

function tooltip2(event)	// gestione tooltip
	{
      document.getElementById("error2").style.visibility="visible";
      if(event.type=="mouseover")
		{
           document.getElementById("error2").style.visibility="visible";
        }
       else if(event.type=="mouseout")
		{
          document.getElementById("error2").style.visibility="hidden";
        }
     }
 </script>

<script> 
function PwChecker()		// controllo password
 {
  var pw=document.getElementById("psw").value;
  console.log(pw);
  var pattern=new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})","g");
  var isStrong=pattern.test(pw);
  if(isStrong){
  console.log("strong");
  $("#form").submit();
  }
  else 
   alert("Password non valida!\nInserire una password di 8 caratteri con un carattere maiuscolo,minuscolo,un numero e un carattere speciale tra questi:'!' '@' '#' '\$' '%' '\^' '&' '\*' '\_'");
 }
</script>

<?php
/***************************** StampaIntestazione *****************************/

function stampaIntestazione()
{
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="/OSM/Anagrafe/img/favicon.ico" />
    <title>Nague - Anagrafe Web</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="/OSM/Anagrafe/css/modern-navbar.css">
	
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
	<!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<?php
}

/***************************** login *****************************/

function login()
{
    $ret=false;
    if (isset($_SESSION['loggato'])) 
    {
        if ($_SESSION['loggato']== true)
            $ret=true;
    }
    else
        $_SESSION['loggato']= false;

    return $ret;
}

/***************************** SETUP *****************************/

function setup() // invocata all'inizio di tutte le pagine, tranne login e logout
{
    // echo "entro in setup()";
    session_start(); // avvia la sessione (usa i cookie per salvare lo stato:in questo caso, per ricordarsi se l'utente è loggato)
    /*

  if (isset($_SESSION['tempo_max']))
   {
     $_SESSION['tempo_max'] = $_SERVER['REQUEST_TIME']; 
     if (time() > ($_SESSION['login_time'] + 10))
	 */
    if (isset($_SESSION['login_time']))
    {
        $time = $_SESSION['login_time']; 
        $_SESSION['login_time'] = $_SERVER['REQUEST_TIME'];
        if (time() > ($time + 300))
        {	// Passati 5 minuti, distruggi la sessione.       
            session_unset();
            session_destroy();
            //   echo "scaduto tempo di sessione: esco()";
            header("Location: /OSM/Anagrafe/index.php");
        }
    }
}


/*****************Paginazione*********************/
function unsetPag($file){		// reset variabili di sessione
    switch($file){
        case "gest_morance.php":
            unset($_SESSION['pag_c']);
		    unset($_SESSION['ord_c']);
			unset($_SESSION['campo_c']);

            unset($_SESSION['pag_p']);
			unset($_SESSION['ord_p']); 
			unset($_SESSION['campo_p']);
            break;
        case "gest_case.php":
            unset($_SESSION['pag_m']);
		    unset($_SESSION['ord_m']);
		    unset($_SESSION['campo_m']);

            unset($_SESSION['pag_p']);
		    unset($_SESSION['ord_p']);
			unset($_SESSION['campo_p']);
            break;
        case "gest_persone.php":
            unset($_SESSION['pag_m']);
		    unset($_SESSION['ord_m']);
			unset($_SESSION['campo_m']);

            unset($_SESSION['pag_c']);
		    unset($_SESSION['ord_c']);
			unset($_SESSION['campo_c']);
            break;
    }
}

/*
*** Paginazione(): ritorna la pagina che deve essere visualizzata
*** cur_page = pagina corrente
*** pagina =
*** subpag =
*** return pag: pagina da visualizzare
*/
function Paginazione($cur_page, $pagina, $subpag=null){
 //  echo "cur_page = ". $cur_page;
    if(is_null($subpag))
		$subpag=$pagina;//Se il parametro opzionale viene omesso,viene impostato al valore di $pagina
    if($cur_page !=0)
    {			//Se non è la prima volta che accedo ad una pagina
        if(isset($_SESSION[$pagina][$subpag]))
        {//Se la sessione è già impostata,l'attribuisco a $pag
            $pag=$cur_page;
            $_SESSION[$pagina][$subpag]=$pag;   
            return $pag;
        }
        else
        {//Se la sessione non è impostata
            $pag=$cur_page;
            $_SESSION[$pagina][$subpag]=$pag; 
            return $pag;
            //     echo $pag;
        }     
    }
    else
    {//Se il get non è impostato(come ad esempio quando apro per la prima volta gestione case)
        if (isset($_SESSION[$pagina][$subpag]))
        {//Se la sessione è già impostata
            $pag=$_SESSION[$pagina][$subpag];    
            return $pag;
        }else
        {//se accedo per la primissima volta alla pagina 
            $pag=1;
            $_SESSION[$pagina][$subpag]=$pag;
            return $pag;
        }
    }    
}

/************  IsLogged: controllo che l'utente loggato possa accedere alle funzionalità **********/

// Se il parametro viene passato,significa che è un utente "Utente" o "Amministratore".
// Se non viene passato viene impostato di default a NULL 
// $perm_rich = permesso utente richiesto per accedere alla funzionalità
function isLogged($perm_rich=null)
 {
//	 echo "loggato=". $_SESSION['loggato'];
//	 echo "permesso =".$_SESSION['tipo'];
//   echo "permeso richiesto=". $perm_rich;

  if(!isset($_SESSION['loggato']) || !$_SESSION['loggato'])
      header("Location: /OSM/Anagrafe/index.php");
         
  if($perm_rich == "amministratore")
	{
     if($_SESSION['tipo']== "gestore" || $_SESSION['tipo']== "utente")
        header("Location: /OSM/Anagrafe/index.php");
    }
  else
  if($perm_rich == "gestore")
   {
     if($_SESSION['tipo']=="utente")
        header("Location: /OSM/Anagrafe/index.php");
   }

 }

/***************** Alert *********************/

function alert($msg)
 {
    echo "<script type='text/javascript'>alert('$msg');</script>";
 }

function EchoMessage($msg, $redirect)
 {
    echo '<script type="text/javascript">
    alert("' . $msg . '")
    window.location.href = "'.$redirect.'"
    </script>';
 }

/***************** my_random_bytes (usato per il Salt) *********************/

 function my_random_bytes($length)
   {
        $characters = '0123456789';
        $characters_length = strlen($characters);
        $output = '';
        for ($i = 0; $i < $length; $i++)
            $output .= $characters[rand(0, $characters_length - 1)];

        return $output;
   }

?>