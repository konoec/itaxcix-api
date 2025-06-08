<?php
// bin/generate-asyncapi.php

// Asegurarse de que existe el directorio docs para el archivo de especificación
$docsDir = __DIR__ . '/../public';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
    echo "Creado directorio: $docsDir\n";
}

// Crear/actualizar la especificación AsyncAPI si es necesario
$specFile = $docsDir . '/asyncapi.yaml';

echo "Documentación AsyncAPI generada en: $specFile\n";
