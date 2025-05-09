<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\EstadoUsuario;

class EstadoUsuarioRepository extends EntityRepository {
    public function getById(int $id): ?EstadoUsuario {
        return $this->find($id);
    }

    public function save(EstadoUsuario $estadoUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($estadoUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(EstadoUsuario $estadoUsuario, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($estadoUsuario);

        if ($flush) {
            $entityManager->flush();
        }
    }
}