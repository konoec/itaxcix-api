<?php

namespace itaxcix\repositories\ubicacion;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\ubicacion\Departamento;

class DepartamentoRepository extends EntityRepository {
    public function getOrCreateUbigeoOrName(string $ubigeo, string $nombre): Departamento {

        // Buscar por ubigeo primero
        $departamento = $this->findOneBy(['ubigeo' => $ubigeo]);

        // Si no se encuentra, buscar por nombre
        if (!$departamento) {
            $departamento = $this->findOneBy(['nombre' => $nombre]);
        }

        // Si tampoco se encuentra, crear uno nuevo
        if (!$departamento) {
            $departamento = new Departamento();
            $departamento->setNombre($nombre);
            $departamento->setUbigeo($ubigeo);
            $this->save($departamento);
        }

        return $departamento;
    }

    public function save(Departamento $departamento, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($departamento);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Departamento $departamento, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($departamento);

        if ($flush) {
            $entityManager->flush();
        }
    }
}