<?php

namespace itaxcix\repositories\persona;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\persona\TipoDocumento;

class TipoDocumentoRepository extends EntityRepository {

    /**
     * Busca un TipoDocumento por su ID.
     *
     * @param int $id El ID del TipoDocumento a buscar.
     * @return TipoDocumento|null Retorna el TipoDocumento si se encuentra, o null si no.
     */
    public function findById(int $id): ?TipoDocumento {
        return $this->find($id);
    }

    public function getById(int $id): ?TipoDocumento {
        return $this->find($id);
    }

    public function save(TipoDocumento $tipoDocumento, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tipoDocumento);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(TipoDocumento $tipoDocumento, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($tipoDocumento);

        if ($flush) {
            $entityManager->flush();
        }
    }
}