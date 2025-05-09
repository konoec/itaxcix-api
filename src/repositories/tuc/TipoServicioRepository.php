<?php

namespace itaxcix\repositories\tuc;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\tuc\TipoServicio;

class TipoServicioRepository extends EntityRepository {
    public function getOrCreateByName(string $nombre): TipoServicio {
        // Buscar primero por nombre
        $tipoServicio = $this->findOneBy(['nombre' => $nombre]);

        // Si no se encuentra, crear nuevo tipo de servicio
        if (!$tipoServicio) {
            $tipoServicio = new TipoServicio();
            $tipoServicio->setNombre($nombre);
            $tipoServicio->setActivo(true);

            $this->save($tipoServicio);
        }

        return $tipoServicio;
    }

    public function save(TipoServicio $tipoServicio, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tipoServicio);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(TipoServicio $tipoServicio, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($tipoServicio);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
