<?php

namespace itaxcix\repositories\tuc;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\tuc\ModalidadTuc;

class ModalidadTucRepository extends EntityRepository {
    public function getOrCreateByName(string $nombre): ModalidadTuc {
        // Buscar primero por nombre
        $modalidad = $this->findOneBy(['nombre' => $nombre]);

        // Si no se encuentra, crear nueva modalidad
        if (!$modalidad) {
            $modalidad = new ModalidadTuc();
            $modalidad->setNombre($nombre);
            $modalidad->setActivo(true);

            $this->save($modalidad);
        }

        return $modalidad;
    }

    public function save(ModalidadTuc $modalidadTuc, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($modalidadTuc);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(ModalidadTuc $modalidadTuc, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($modalidadTuc);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
