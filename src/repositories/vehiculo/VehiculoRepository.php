<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;

class VehiculoRepository extends EntityRepository {

    /**
     * Verifica si existe un vehículo con la placa especificada.
     *
     * @param string $placa La placa del vehículo a buscar.
     * @return bool True si existe, false en caso contrario.
     */
    public function existsByPlaca(string $placa): bool {
        $result = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.placa = :placa')
            ->setParameter('placa', $placa)
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$result > 0;
    }

    public function save($vehiculo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($vehiculo);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove($vehiculo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($vehiculo);

        if ($flush) {
            $entityManager->flush();
        }
    }
}