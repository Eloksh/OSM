<?php
session_start();

session_unset();
session_destroy();

//echo "<p style=\"color:red;\">Sessione conclusa:arrivederci</p>";

header("Location: /OSM/Anagrafe/index.php"); // reindirizzo sulla pagina di login

?>