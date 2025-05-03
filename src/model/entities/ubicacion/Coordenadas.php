<?php

namespace itaxcix\model\entities\ubicacion;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_coordenadas')]
class Coordenadas {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'coor_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'coor_nombre', type: 'string', length: 100)]
    private string $nombre;

    #[ORM\ManyToOne(targetEntity: Distrito::class)]
    #[ORM\JoinColumn(name: 'coor_distrito_id', referencedColumnName: 'dist_id')]
    private ?Distrito $distrito = null;

    #[ORM\Column(name: 'coor_latitud', type: 'string', length: 20)]
    private string $latitud;

    #[ORM\Column(name: 'coor_longitud', type: 'string', length: 20)]
    private string $longitud;
}