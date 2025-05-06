<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\usuario\EstadoUsuario;

class EstadoUsuarioRepository extends EntityRepository
{
    public function findByName(string $name): ?EstadoUsuario
    {
        return $this->findOneBy(['nombre' => $name]);
    }

    public function getDefault(): ?EstadoUsuario
    {
        return $this->findOneBy([], ['id' => 'ASC']);
    }
}