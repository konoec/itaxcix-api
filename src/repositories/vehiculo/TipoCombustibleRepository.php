<?php

namespace itaxcix\repositories\vehiculo;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\vehiculo\TipoCombustible;

class TipoCombustibleRepository extends EntityRepository {
    public function getOrCreateByName(string $nombre): TipoCombustible {
        $combustible = $this->findOneBy(['nombre' => $nombre]);

        if (!$combustible) {
            $combustible = new TipoCombustible();
            $combustible->setNombre($nombre);
            $combustible->setActivo(true);

            $this->save($combustible);
        }

        return $combustible;
    }
    public function save(TipoCombustible $tipoCombustible, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tipoCombustible);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(TipoCombustible $tipoCombustible, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($tipoCombustible);

        if ($flush) {
            $entityManager->flush();
        }
    }
}