<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\vehiculo\CategoriaVehiculo;

class CategoriaVehiculoRepository extends EntityRepository {
    public function getOrCreateByName(string $nombre): CategoriaVehiculo {
        $categoria = $this->findOneBy(['nombre' => $nombre]);

        if (!$categoria) {
            $categoria = new CategoriaVehiculo();
            $categoria->setNombre($nombre);
            $categoria->setActivo(true);

            $this->save($categoria);
        }

        return $categoria;
    }
    public function save(CategoriaVehiculo $categoriaVehiculo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($categoriaVehiculo);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(CategoriaVehiculo $categoriaVehiculo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($categoriaVehiculo);

        if ($flush) {
            $entityManager->flush();
        }
    }
}