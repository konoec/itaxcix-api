<?php

// Incluye el archivo de inicialización
require_once __DIR__ . '/../src/config/Bootstrap.php';

use Illuminate\Database\Capsule\Manager;
use iTaxCix\models\common\TbTipoDocumento;

try {
    // Prueba la conexión
    $connection = Manager::connection();
    echo "Conexión exitosa a PostgreSQL\n";

    // Consulta todos los registros de la tabla
    $documentos = TbTipoDocumento::all();

    foreach ($documentos as $documento) {
        echo "ID: {$documento->tdoc_id}, Nombre: {$documento->tdoc_nombre}\n";
    }
} catch (\Exception $e) {
    echo "Error al conectar: " . $e->getMessage();
}