<?php

namespace itaxcix\repositories\tuc;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\tuc\EstadoTuc;

class EstadoTucRepository extends EntityRepository {
    public function getByNombre(string $nombre): ?EstadoTuc {
        return $this->findOneBy(['nombre' => $nombre]);
    }

    public function getById(int $id): ?EstadoTuc {
        return $this->find($id);
    }

    public function save(EstadoTuc $estadoTuc, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($estadoTuc);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(EstadoTuc $estadoTuc, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($estadoTuc);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
