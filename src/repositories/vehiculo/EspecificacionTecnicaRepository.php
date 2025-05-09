<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;

class EspecificacionTecnicaRepository extends EntityRepository {
    public function save($especificacionTecnica, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($especificacionTecnica);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove($especificacionTecnica, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($especificacionTecnica);

        if ($flush) {
            $entityManager->flush();
        }
    }
}