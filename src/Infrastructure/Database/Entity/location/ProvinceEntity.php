<?php

namespace itaxcix\Infrastructure\Database\Entity\location;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_provincia')]
class ProvinceEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'prov_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'prov_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private ?string $name = null;
    #[ORM\ManyToOne(targetEntity: DepartmentEntity::class)]
    #[ORM\JoinColumn(name: 'prov_departamento_id', referencedColumnName: 'depa_id')]
    private ?DepartmentEntity $department = null;
    #[ORM\Column(name: 'prov_ubigeo', type: 'string', length: 6, unique: true, nullable: true)]
    private ?string $ubigeo = null;
}