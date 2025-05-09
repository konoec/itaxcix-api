<?php

namespace itaxcix\repositories\tuc;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\tuc\RutaServicio;

class RutaServicioRepository extends EntityRepository {
    public function findByTramite($tramite): ?RutaServicio {
        return $this->findOneBy(['tramite' => $tramite]);
    }
    public function save(RutaServicio $rutaServicio, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($rutaServicio);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(RutaServicio $rutaServicio, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($rutaServicio);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
