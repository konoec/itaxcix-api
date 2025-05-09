<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;

class VehiculoUsuarioRepository extends EntityRepository {
    public function save($vehiculoUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($vehiculoUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove($vehiculoUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($vehiculoUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }
}