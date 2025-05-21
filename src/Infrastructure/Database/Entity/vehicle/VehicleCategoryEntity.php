<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_categoria_vehiculo')]
class VehicleCategoryEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cate_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'cate_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private ?string $name = null;
    #[ORM\Column(name: 'cate_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    protected bool $active = true;
}