<?php

/**
 * API endpoint para obtener la lista de encargados disponibles.
 */

require_once __DIR__ . '/../config/Database.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare('SELECT id, run, first_name, last_name, second_last_name, address, phone FROM users');
    $stmt->execute();
    $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $managers]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener encargados: ' . $e->getMessage()]);
}
