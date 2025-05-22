<?php
// Ottiene una persona per ID
header("Content-Type: application/json; charset=UTF-8");
require_once '../config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID mancante"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM persone WHERE ID = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($persona) {
        echo json_encode($persona);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Persona non trovata"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
