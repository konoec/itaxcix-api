<?php

namespace itaxcix\Infrastructure\Database\Entity\travel;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_estado_viaje')]
class TravelStatusEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'esta_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'esta_nombre', type: 'string', length: 50)]
    private string $name;
    #[ORM\Column(name: 'esta_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
}