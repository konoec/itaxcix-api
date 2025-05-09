<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\vehiculo\Color;

class ColorRepository extends EntityRepository {
    public function getOrCreateByName(string $nombre): Color {
        // Buscar por nombre primero
        $color = $this->findOneBy(['nombre' => $nombre]);

        // Si no se encuentra, crear nuevo color
        if (!$color) {
            $color = new Color();
            $color->setNombre($nombre);
            $color->setActivo(true);

            $this->save($color);
        }

        return $color;
    }

    public function save($color, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($color);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove($color, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($color);

        if ($flush) {
            $entityManager->flush();
        }
    }
}