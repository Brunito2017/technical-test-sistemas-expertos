<?php

require_once __DIR__ . '/app/config/Database.php';

$users = [
    [
        'run' => '12345678-9',
        'first_name' => 'Juan',
        'last_name' => 'Pérez',
        'second_last_name' => 'Gómez',
        'address' => 'Calle Falsa 123',
        'phone' => '912345678',
    ],
    [
        'run' => '98765432-1',
        'first_name' => 'María',
        'last_name' => 'López',
        'second_last_name' => 'Ramírez',
        'address' => 'Av. Siempre Viva 742',
        'phone' => '987654321',
    ],
];

try {
    $db = Database::getConnection();
    $db->beginTransaction();
    $stmt = $db->prepare('INSERT INTO users (run, first_name, last_name, second_last_name, address, phone) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($users as $user) {
        $stmt->execute([
            $user['run'],
            $user['first_name'],
            $user['last_name'],
            $user['second_last_name'],
            $user['address'],
            $user['phone'],
        ]);
    }
    $db->commit();
    echo "Usuarios insertados correctamente.\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error insertando usuarios: " . $e->getMessage() . "\n";
}
