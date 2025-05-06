<?php

namespace itaxcix\repositories\usuario;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\persona\Persona;
use itaxcix\models\entities\usuario\Usuario;

class UsuarioRepository extends EntityRepository
{
    public function findByAlias(string $alias): ?Usuario
    {
        return $this->findOneBy(['alias' => $alias]);
    }

    public function findByPerson(Persona $persona): ?Usuario
    {
        return $this->findOneBy(['persona' => $persona]);
    }

    public function save(Usuario $usuario): void
    {
        $this->_em->persist($usuario);
        $this->_em->flush();
    }
}