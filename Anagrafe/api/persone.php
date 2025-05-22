<?php
// GET http://localhost/OSM/Anagrafe/api/persone.php?nominativo=fassu


header("Content-Type: application/json");
require_once 'config.php';

$nominativo = isset($_GET['nominativo']) ? trim($_GET['nominativo']) : '';

if ($nominativo === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Parametro "nominativo" mancante.']);
    exit;
}

try {
    $sql = "SELECT * FROM persone 
            WHERE NOMINATIVO LIKE :nominativo
            AND (DATA_FINE_VAL IS NULL OR DATA_FINE_VAL = CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':nominativo' => "%$nominativo%"]);
    $persone = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($persone);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore nella query: ' . $e->getMessage()]);
}
?>