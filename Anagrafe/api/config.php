<?php
$host = 'localhost';
$port = '3306'; // porta custom
$dbname = 'my_ntchanguetest';
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Connessione al database fallita: ' . $e->getMessage()]);
    exit;
}
?>

