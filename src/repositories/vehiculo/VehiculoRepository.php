<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\vehiculo\Vehiculo;

class VehiculoRepository extends EntityRepository {

    /**
     * Verifica si existe un vehículo con la placa especificada.
     *
     * @param string $placa La placa del vehículo a buscar.
     * @return array Un array con el resultado de la búsqueda.
     */
    public function existsByPlaca(string $placa): array {
        $qb = $this->createQueryBuilder('v')
            ->select('v, vu')
            ->leftJoin('v.vehiculoUsuarios', 'vu', 'WITH', 'vu.activo = true')
            ->where('v.placa = :placa')
            ->setParameter('placa', $placa);

        $results = $qb->getQuery()->getArrayResult();

        if (empty($results)) {
            return ['existe' => false];
        }

        $vehiculo = $results[0];
        $relacionActiva = !empty($vehiculo['vehiculoUsuarios']);

        return [
            'existe' => true,
            'id' => $vehiculo['id'],
            'relacionActiva' => $relacionActiva
        ];
    }



    public function save(Vehiculo $vehiculo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($vehiculo);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Vehiculo $vehiculo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($vehiculo);

        if ($flush) {
            $entityManager->flush();
        }
    }
}