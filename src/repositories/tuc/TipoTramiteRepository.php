<?php

namespace itaxcix\repositories\tuc;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\tuc\TipoTramite;

class TipoTramiteRepository extends EntityRepository {
    public function getOrCreateByName(string $nombre): TipoTramite {
        // Buscar primero por nombre
        $tipoTramite = $this->findOneBy(['nombre' => $nombre]);

        // Si no se encuentra, crear nuevo tipo de trámite
        if (!$tipoTramite) {
            $tipoTramite = new TipoTramite();
            $tipoTramite->setNombre($nombre);
            $tipoTramite->setActivo(true);

            $this->save($tipoTramite);
        }

        return $tipoTramite;
    }

    public function save(TipoTramite $tipoTramite, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tipoTramite);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(TipoTramite $tipoTramite, bool $flush = true): void {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($tipoTramite);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
