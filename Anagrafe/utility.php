<?php
$config_path = __DIR__;
$util = $config_path .'/util.php';
require $util;
setup();
?>
<html>
<?php stampaIntestazione(); ?>
<body>
<?php stampaNavbar(); ?>
  <br>
  <a href="http://localhost/OSM/Anagrafe/db/db2geojson.php">
  DB--> geojson</a>&nbsp;&nbsp;
  (legge da DB e produce il file points.geojson)
  <br>
  <br>
 
  <a href="http://localhost/OSM/Anagrafe/db/geojson2db.php">
  geojson-->DB </a>&nbsp;&nbsp;
  (carica il DB a partire dal file points.geojson.)
  <br>
  <br>
  
  <a href="http://localhost/OSM/Anagrafe/db/ripristina.php">
  ripristina</a>&nbsp;&nbsp;&nbsp;&nbsp;
  ripristina la situazione di partenza su DB e su file points.geojson: Attenzione: inserisce 1 sola casa 
  <br>
 
 <a href="https://drive.google.com/file/d/1VOXNtxo_ULb5xbqlJeVmjNz9vhz2insi/view?usp=sharing" target=new>
  Segnalazioni di errore</a>&nbsp;&nbsp;&nbsp;&nbsp;
  Accede al file delle segnalazioni condiviso
  <br>
 
 </body>

</html>