<?php

/*********  ITISAVOGADRO.ORG      
$host = "mysql.itisavogadro.org";
$user = "acarlone";
$pass = "Cambiami_2018";
$database = "acarlone";
********/


/*ALTERVISTA Test Enviroment
$database = "my_ntchanguetest";
$host = "localhost";
$user = "ntchanguetest";
$pass = "ErCHj4SJJ6Mm";
*/
/* LOCALHOST
$database = "my_ntchanguetest";
$host = "localhost";
$user = "root";
$pass = "";
/* LOCALHOST*/
$database = "my_ntchanguetest";
$host = "localhost";
$user = "root";
$pass = "";

/*********ALTERVISTA
$database = "my_alfonsocarlone";
$user = "alfonsocarlone";
$pass = "";

**********/


// Connessione al DB
$conn = mysqli_connect($host, $user, $pass, $database);

// check connection 
if (mysqli_connect_errno()) {
    printf("Errore Connessione al DB: %s\n", mysqli_connect_error());
    exit();
}
?>