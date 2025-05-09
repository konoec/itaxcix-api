<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\vehiculo\ClaseVehiculo;

class ClaseVehiculoRepository extends EntityRepository {
    public function getOrCreateByName(string $nombre): ClaseVehiculo {
        $clase = $this->findOneBy(['nombre' => $nombre]);

        if (!$clase) {
            $clase = new ClaseVehiculo();
            $clase->setNombre($nombre);
            $clase->setActivo(true);

            $this->save($clase);
        }

        return $clase;
    }

    public function save(ClaseVehiculo $claseVehiculo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($claseVehiculo);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(ClaseVehiculo $claseVehiculo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($claseVehiculo);

        if ($flush) {
            $entityManager->flush();
        }
    }
}