<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\TipoCodigoUsuario;

class TipoCodigoUsuarioRepository extends EntityRepository {
    /**
     * Obtiene el tipo de código por nombre.
     *
     * @param string $nombre
     * @return TipoCodigoUsuario|null
     */
    public function findOneByName(string $nombre): ?TipoCodigoUsuario {
        return $this->findOneBy(['nombre' => $nombre]);
    }
}