<?php

namespace itaxcix\repositories\tuc;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\tuc\Empresa;

class EmpresaRepository extends EntityRepository {
    public function getOrCreateByRucOrName(string $ruc, string $nombre): Empresa {
        // Buscar primero por RUC
        $empresa = $this->findOneBy(['ruc' => $ruc]);

        // Si no se encuentra por RUC, buscar por nombre
        if (!$empresa) {
            $empresa = $this->findOneBy(['nombre' => $nombre]);
        }

        // Si tampoco se encuentra, crear nueva empresa
        if (!$empresa) {
            $empresa = new Empresa();
            $empresa->setRuc($ruc);
            $empresa->setNombre($nombre);
            $empresa->setActivo(true);

            $this->save($empresa, false); // Persistir sin flush inmediato
        }

        return $empresa;
    }

    public function save(Empresa $empresa, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($empresa);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Empresa $empresa, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($empresa);

        if ($flush) {
            $entityManager->flush();
        }
    }
}