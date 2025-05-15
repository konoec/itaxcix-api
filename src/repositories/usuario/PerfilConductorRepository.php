<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\PerfilConductor;

class PerfilConductorRepository extends EntityRepository {
    public function findOneByUserId(int $userId): ?PerfilConductor {
        return $this->createQueryBuilder('p')
            ->where('p.usuario = :usuarioId')
            ->setParameter('usuarioId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function save(PerfilConductor $perfilConductor, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($perfilConductor);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(PerfilConductor $perfilConductor, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($perfilConductor);

        if ($flush) {
            $entityManager->flush();
        }
    }
}