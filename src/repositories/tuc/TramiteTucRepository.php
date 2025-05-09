<?php

namespace itaxcix\repositories\tuc;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\tuc\TramiteTuc;

class TramiteTucRepository extends EntityRepository {
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
