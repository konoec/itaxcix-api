<?php

namespace itaxcix\Infrastructure\Database\Entity\infraction;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_gravedad_infraccion')]
class InfractionSeverityEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'grav_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'grav_nombre', type: 'string', length: 100, unique: true)]
    private string $name;
    #[ORM\Column(name: 'grav_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
}