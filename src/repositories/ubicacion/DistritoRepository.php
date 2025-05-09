<?php

namespace itaxcix\repositories\ubicacion;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\ubicacion\Distrito;
use itaxcix\models\entities\ubicacion\Provincia;

class DistritoRepository extends EntityRepository {
    public function getOrCreateUbigeoOrName(string $ubigeo, string $nombre, Provincia $provincia): Distrito {

        // Buscar por ubigeo primero
        $distrito = $this->findOneBy(['ubigeo' => $ubigeo]);

        // Si no se encuentra, buscar por nombre y provincia
        if (!$distrito) {
            $distrito = $this->createQueryBuilder('d')
                ->where('d.nombre = :nombre AND d.provincia = :provincia')
                ->setParameter('nombre', $nombre)
                ->setParameter('provincia', $provincia)
                ->getQuery()
                ->getOneOrNullResult();
        }

        // Si tampoco se encuentra, crear nuevo distrito
        if (!$distrito) {
            $distrito = new Distrito();
            $distrito->setNombre($nombre);
            $distrito->setUbigeo($ubigeo);
            $distrito->setProvincia($provincia);

            $this->save($distrito);
        }

        return $distrito;
    }

    public function save(Distrito $distrito, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($distrito);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Distrito $distrito, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($distrito);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
