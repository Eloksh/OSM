<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

try {
    $query = "SELECT ID, NOMINATIVO, SESSO, MATRICOLA_STUD, DATA_NASCITA, DATA_MORTE, DATA_INIZIO_VAL, DATA_FINE_VAL FROM persone";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $persone = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($persone);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Errore nella query: " . $e->getMessage()]);
}
?>
