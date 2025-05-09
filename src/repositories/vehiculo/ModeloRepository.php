<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\vehiculo\Marca;
use itaxcix\models\entities\vehiculo\Modelo;

class ModeloRepository extends EntityRepository {
    public function getOrCreateByNameAndMarca(string $nombre, Marca $marca): Modelo {
        // Buscar por nombre y marca primero
        $modelo = $this->createQueryBuilder('m')
            ->where('m.nombre = :nombre AND m.marca = :marca')
            ->setParameter('nombre', $nombre)
            ->setParameter('marca', $marca)
            ->getQuery()
            ->getOneOrNullResult();

        // Si no se encuentra, crear nuevo modelo
        if (!$modelo) {
            $modelo = new Modelo();
            $modelo->setNombre($nombre);
            $modelo->setMarca($marca);
            $modelo->setActivo(true);

            $this->save($modelo);
        }

        return $modelo;
    }

    public function save(Modelo $modelo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($modelo);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Modelo $modelo, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($modelo);

        if ($flush) {
            $entityManager->flush();
        }
    }
}