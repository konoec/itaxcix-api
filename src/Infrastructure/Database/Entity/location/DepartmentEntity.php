<?php

namespace itaxcix\Infrastructure\Database\Entity\location;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_departamento')]
class DepartmentEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'depa_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'depa_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private ?string $name = null;
    #[ORM\Column(name: 'depa_ubigeo', type: 'string', length: 6, unique: true, nullable: true)]
    private ?string $ubigeo = null;
}