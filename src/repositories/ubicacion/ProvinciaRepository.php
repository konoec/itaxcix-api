<?php

namespace itaxcix\repositories\ubicacion;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\ubicacion\Departamento;
use itaxcix\models\entities\ubicacion\Provincia;

class ProvinciaRepository extends EntityRepository {
    public function getOrCreateUbigeoOrName(string $ubigeo, string $nombre, Departamento $departamento): Provincia {

        // Buscar por ubigeo primero
        $provincia = $this->findOneBy(['ubigeo' => $ubigeo]);

        // Si no se encuentra, buscar por nombre dentro del departamento
        if (!$provincia) {
            $provincia = $this->createQueryBuilder('p')
                ->where('p.nombre = :nombre AND p.departamento = :departamento')
                ->setParameter('nombre', $nombre)
                ->setParameter('departamento', $departamento)
                ->getQuery()
                ->getOneOrNullResult();
        }

        // Si tampoco se encuentra, crear nueva provincia
        if (!$provincia) {
            $provincia = new Provincia();
            $provincia->setNombre($nombre);
            $provincia->setUbigeo($ubigeo);
            $provincia->setDepartamento($departamento);

            $this->save($provincia);
        }

        return $provincia;
    }

    public function save(Provincia $provincia, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($provincia);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Provincia $provincia, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($provincia);

        if ($flush) {
            $entityManager->flush();
        }
    }
}