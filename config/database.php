<?php

$host = 'db';
$dbname = 'db_itaxcix';
$user = 'itaxcix_user';
$password = 'itaxcix';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo json_encode(['status' => 'success', 'message' => 'Conectado a PostgreSQL']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo conectar: ' . $e->getMessage()]);
}