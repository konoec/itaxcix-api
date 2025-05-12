<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\VehiculoUsuario;

class VehiculoUsuarioRepository extends EntityRepository {
    public function save(VehiculoUsuario $vehiculoUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($vehiculoUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(VehiculoUsuario $vehiculoUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($vehiculoUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }
}