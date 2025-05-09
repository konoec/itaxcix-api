<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;

class PerfilConductorRepository extends EntityRepository {
    public function save($perfilConductor, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($perfilConductor);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove($perfilConductor, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($perfilConductor);

        if ($flush) {
            $entityManager->flush();
        }
    }
}