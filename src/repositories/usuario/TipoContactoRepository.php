<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\TipoContacto;

class TipoContactoRepository extends EntityRepository
{
    public function findByName(string $name): ?TipoContacto
    {
        return $this->findOneBy(['nombre' => $name]);
    }
}