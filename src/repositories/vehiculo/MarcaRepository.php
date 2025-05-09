<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\vehiculo\Marca;

class MarcaRepository extends EntityRepository {
    public function getOrCreateByName(string $nombre): Marca {
        // Buscar por nombre primero
        $marca = $this->findOneBy(['nombre' => $nombre]);

        // Si no se encuentra, crear nueva marca
        if (!$marca) {
            $marca = new Marca();
            $marca->setNombre($nombre);
            $marca->setActivo(true);

            $this->save($marca);
        }

        return $marca;
    }

    public function save(Marca $marca, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($marca);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Marca $marca, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($marca);

        if ($flush) {
            $entityManager->flush();
        }
    }
}