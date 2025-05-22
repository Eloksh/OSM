<?php
/*---------------------------------
- index.php: Esempi di chiamate
-
- GET /api/persone → restituisce tutte le persone
- GET /api/persone?search=nome → ricerca per nominativo
- POST /api/persone → inserisce una nuova persona

ricerca di tutti:
GET http://localhost/OSM/Anagrafe/api/index.php

ricerca per nominativo:
GET http://localhost/OSM/Anagrafe/api/persone.php?nominativo=fassu

creazione:
POST http://localhost/api/persone/index.php

Content-Type: application/json

{
  "NOMINATIVO": "Mario Rossi",
  "SESSO": "M",
  "MATRICOLA_STUD": "123456",
  "DATA_NASCITA": "1985-04-20",
  "DATA_MORTE": null,
  "DATA_INIZIO_VAL": "2020-01-01",
  "DATA_FINE_VAL": null
}

----------------------------------------------------------------*/
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Per le richieste POST
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'config.php';

// Metodo GET → Tutti o ricerca
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : null;

    try {
        if ($search) {
            $stmt = $conn->prepare("SELECT * FROM persone WHERE NOMINATIVO LIKE :search");
            $stmt->bindParam(':search', $search);
        } else {
            $stmt = $conn->prepare("SELECT * FROM persone");
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($results);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

    exit;
}

// Metodo POST → Inserimento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['NOMINATIVO'], $data['SESSO'])) {
        http_response_code(400);
        echo json_encode(["error" => "NOMINATIVO e SESSO sono obbligatori"]);
        exit;
    }

    $query = "INSERT INTO persone (ID, NOMINATIVO, SESSO, MATRICOLA_STUD, DATA_NASCITA, DATA_MORTE, DATA_INIZIO_VAL, DATA_FINE_VAL)
              VALUES (NULL, :nominativo, :sesso, :matricola, :nascita, :morte, :inizio, :fine)";

    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':nominativo' => $data['NOMINATIVO'],
            ':sesso' => $data['SESSO'],
            ':matricola' => $data['MATRICOLA_STUD'] ?? null,
            ':nascita' => $data['DATA_NASCITA'] ?? null,
            ':morte' => $data['DATA_MORTE'] ?? null,
            ':inizio' => $data['DATA_INIZIO_VAL'] ?? null,
            ':fine' => $data['DATA_FINE_VAL'] ?? null,
        ]);

        http_response_code(201);
        echo json_encode(["message" => "Persona creata con successo", "ID_inserito" => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

    exit;
}

// Metodo non supportato
http_response_code(405);
echo json_encode(["error" => "Metodo non supportato. Usa GET o POST."]);

