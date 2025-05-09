<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\Rol;

class RolRepository extends EntityRepository {
    public function getByNombre(string $nombre): ?Rol {
        return $this->findOneBy(['nombre' => $nombre]);
    }

    public function save(Rol $rol, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($rol);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Rol $rol, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($rol);

        if ($flush) {
            $entityManager->flush();
        }
    }
}