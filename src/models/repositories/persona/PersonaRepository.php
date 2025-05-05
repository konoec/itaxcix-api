<?php

namespace itaxcix\models\repositories\persona;

use Doctrine\ORM\EntityRepository;
use itaxcix\models\entities\persona\Persona;

class PersonaRepository extends EntityRepository
{
    public function findByDocument(string $document): ?Persona
    {
        return $this->findOneBy(['documento' => $document]);
    }

    public function save(Persona $persona): void
    {
        $this->_em->persist($persona);
        $this->_em->flush();
    }
}