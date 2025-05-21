<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_tipo_combustible')]
class FuelTypeEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tipo_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'tipo_nombre', type: 'string', length: 50, unique: true, nullable: false)]
    private string $name;
    #[ORM\Column(name: 'tipo_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}