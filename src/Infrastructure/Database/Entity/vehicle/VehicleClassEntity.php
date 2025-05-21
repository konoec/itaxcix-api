<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_clase_vehiculo')]
class VehicleClassEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'clas_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'clas_nombre', type: 'string', length: 50, unique: true, nullable: false)]
    private string $name;
    #[ORM\Column(name: 'clas_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}