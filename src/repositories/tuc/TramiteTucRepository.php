<?php

namespace itaxcix\repositories\tuc;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\tuc\EstadoTuc;
use itaxcix\models\entities\tuc\TramiteTuc;
use itaxcix\models\entities\vehiculo\Vehiculo;

class TramiteTucRepository extends EntityRepository {

    /**
     * Busca trámites TUC vigentes para un vehículo específico.
     * - Estado = "Activo"
     * - Fecha de caducidad > hoy
     * - Ordenados por fecha de emisión descendente
     *
     * @param Vehiculo $vehiculo
     * @param EstadoTuc $estadoTucActivo
     * @return array
     */
    public function findVigentesByVehiculo(Vehiculo $vehiculo, EstadoTuc $estadoTucActivo): array {
        return $this->createQueryBuilder('t')
            ->where('t.vehiculo = :vehiculo')
            ->andWhere('t.estado = :estadoActivo')
            ->andWhere('t.fechaCaducidad > :hoy')
            ->setParameter('vehiculo', $vehiculo)
            ->setParameter('estadoActivo', $estadoTucActivo)
            ->setParameter('hoy', new \DateTime())
            ->orderBy('t.fechaEmision', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findByPlaca(string $placa): ?TramiteTuc {
        return $this->createQueryBuilder('t')
            ->join('t.vehiculo', 'v') // Relación ManyToOne con Vehiculo
            ->where('v.placa = :placa')
            ->setParameter('placa', $placa)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(TramiteTuc $tramiteTuc, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tramiteTuc);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(TramiteTuc $tramiteTuc, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($tramiteTuc);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
