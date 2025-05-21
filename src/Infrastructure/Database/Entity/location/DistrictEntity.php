<?php

namespace itaxcix\Infrastructure\Database\Entity\location;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_distrito')]
class DistrictEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'dist_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'dist_nombre', type: 'string', length: 50, unique: true, nullable: true)]
    private ?string $name = null;
    #[ORM\ManyToOne(targetEntity: ProvinceEntity::class)]
    #[ORM\JoinColumn(name: 'dist_provincia_id', referencedColumnName: 'prov_id')]
    private ?ProvinceEntity $province = null;
    #[ORM\Column(name: 'dist_ubigeo', type: 'string', length: 6, unique: true, nullable: true)]
    private ?string $ubigeo = null;
}