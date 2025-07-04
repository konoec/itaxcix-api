<?php

require_once 'vendor/autoload.php';

use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucProcedureEntity;

// Configurar Doctrine (usando la misma configuración que tu app)
$container = require 'config/container.php';
$entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);

echo "=== DEBUG TUC PROCEDURE QUERY ===\n\n";

// Consulta básica para ver si hay TUCs en la base de datos
echo "1. Verificando TUCs existentes en la base de datos:\n";
$tucCount = $entityManager->createQueryBuilder()
    ->select('COUNT(tp.id)')
    ->from(TucProcedureEntity::class, 'tp')
    ->getQuery()
    ->getSingleScalarResult();

echo "Total TUCs encontradas: $tucCount\n\n";

if ($tucCount > 0) {
    // Mostrar algunas TUCs con sus vehículos
    echo "2. Primeras 5 TUCs con datos completos:\n";
    $tucs = $entityManager->createQueryBuilder()
        ->select('tp, v, c, d, s, t, m')
        ->from(TucProcedureEntity::class, 'tp')
        ->leftJoin('tp.vehicle', 'v')
        ->leftJoin('tp.company', 'c')
        ->leftJoin('tp.district', 'd')
        ->leftJoin('tp.status', 's')
        ->leftJoin('tp.type', 't')
        ->leftJoin('tp.modality', 'm')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();

    foreach ($tucs as $row) {
        $tuc = is_array($row) ? $row[0] : $row;
        echo sprintf(
            "TUC ID: %d, Vehicle: %s, Company: %s, District: %s, Status: %s\n",
            $tuc->getId(),
            $tuc->getVehicle()?->getLicensePlate() ?? 'NULL',
            $tuc->getCompany()?->getName() ?? 'NULL',
            $tuc->getDistrict()?->getName() ?? 'NULL',
            $tuc->getStatus()?->getName() ?? 'NULL'
        );
    }
    echo "\n";
}

// Simular la consulta del repositorio
echo "3. Simulando consulta del repositorio:\n";
$qb = $entityManager->createQueryBuilder()
    ->select('v, m, b, c, f, vc, cat')
    ->from(VehicleEntity::class, 'v')
    ->leftJoin('v.model', 'm')
    ->leftJoin('m.brand', 'b')
    ->leftJoin('v.color', 'c')
    ->leftJoin('v.fuelType', 'f')
    ->leftJoin('v.vehicleClass', 'vc')
    ->leftJoin('v.category', 'cat')
    ->leftJoin('itaxcix\\Infrastructure\\Database\\Entity\\vehicle\\TucProcedureEntity', 'tp', 'WITH', 'tp.vehicle = v')
    ->leftJoin('tp.company', 'tuc_comp')
    ->leftJoin('tp.district', 'tuc_dist')
    ->leftJoin('tp.status', 'tuc_stat')
    ->leftJoin('tp.type', 'tuc_type')
    ->leftJoin('tp.modality', 'tuc_mod')
    ->addSelect('tp', 'tuc_comp', 'tuc_dist', 'tuc_stat', 'tuc_type', 'tuc_mod')
    ->setMaxResults(3);

$sql = $qb->getQuery()->getSQL();
echo "SQL generado:\n$sql\n\n";

$results = $qb->getQuery()->getResult();
echo "4. Resultados obtenidos (" . count($results) . " registros):\n";

foreach ($results as $index => $row) {
    echo "Resultado #$index:\n";
    echo "  Tipo: " . (is_array($row) ? 'Array' : get_class($row)) . "\n";

    if (is_array($row)) {
        echo "  Elementos en array: " . count($row) . "\n";
        foreach ($row as $i => $item) {
            if ($item !== null) {
                echo "    [$i]: " . get_class($item) . "\n";
                if ($item instanceof VehicleEntity) {
                    echo "      Vehicle: " . $item->getLicensePlate() . "\n";
                } elseif ($item instanceof TucProcedureEntity) {
                    echo "      TUC ID: " . $item->getId() . "\n";
                    echo "      Company: " . ($item->getCompany()?->getName() ?? 'NULL') . "\n";
                    echo "      District: " . ($item->getDistrict()?->getName() ?? 'NULL') . "\n";
                }
            } else {
                echo "    [$i]: NULL\n";
            }
        }
    } else {
        echo "  Objeto único: " . get_class($row) . "\n";
        if ($row instanceof VehicleEntity) {
            echo "    Vehicle: " . $row->getLicensePlate() . "\n";
        }
    }
    echo "\n";
}
