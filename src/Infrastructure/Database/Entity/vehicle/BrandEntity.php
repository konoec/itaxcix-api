<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_marca')]
class BrandEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'marc_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'marc_nombre', type: 'string', length: 50, unique: true, nullable: false)]
    private string $name;
    #[ORM\Column(name: 'marc_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}