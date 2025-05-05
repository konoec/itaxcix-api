<?php

namespace itaxcix\models\repositories\persona;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\persona\TipoDocumento;

class TipoDocumentoRepository extends EntityRepository
{
    public function findById(int $id): ?TipoDocumento
    {
        return $this->find($id);
    }
}