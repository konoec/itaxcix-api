<?php

namespace itaxcix\Infrastructure\Database\Entity\location;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_coordenadas')]
class CoordinatesEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'coor_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'coor_nombre', type: 'string', length: 100)]
    private string $name;
    #[ORM\ManyToOne(targetEntity: DistrictEntity::class)]
    #[ORM\JoinColumn(name: 'coor_distrito_id', referencedColumnName: 'dist_id')]
    private ?DistrictEntity $district = null;
    #[ORM\Column(name: 'coor_latitud', type: 'string', length: 20)]
    private string $latitude;
    #[ORM\Column(name: 'coor_longitud', type: 'string', length: 20)]
    private string $longitude;
}