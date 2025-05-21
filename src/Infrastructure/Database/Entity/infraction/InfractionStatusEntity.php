<?php

namespace itaxcix\Infrastructure\Database\Entity\infraction;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_estado_infraccion')]
class InfractionStatusEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'esta_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'esta_nombre', type: 'string', length: 100, unique: true)]
    private string $name;
    #[ORM\Column(name: 'esta_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
}