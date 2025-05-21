<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_modelo')]
class ModelEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'mode_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'mode_nombre', type: 'string', length: 50, nullable: false)]
    private string $name;
    #[ORM\ManyToOne(targetEntity: BrandEntity::class)]
    #[ORM\JoinColumn(name: 'mode_marca_id', referencedColumnName: 'marc_id')]
    private ?BrandEntity $brand = null;
    #[ORM\Column(name: 'mode_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}