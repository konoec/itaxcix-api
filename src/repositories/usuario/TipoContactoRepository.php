<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\TipoContacto;

class TipoContactoRepository extends EntityRepository {

    /**
     * Busca un TipoContacto por su ID.
     *
     * @param int $id El ID del TipoContacto a buscar.
     * @return TipoContacto|null Retorna el TipoContacto si se encuentra, o null si no.
     */
    public function getById(int $id): ?TipoContacto {
        return $this->find($id);
    }

    public function save(TipoContacto $tipoContacto, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tipoContacto);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(TipoContacto $tipoContacto, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($tipoContacto);

        if ($flush) {
            $entityManager->flush();
        }
    }
}