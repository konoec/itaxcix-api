<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_modelo')]
class Modelo {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'mode_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'mode_nombre', type: 'string', length: 50)]
    private string $nombre;

    #[ORM\ManyToOne(targetEntity: Marca::class)]
    #[ORM\JoinColumn(name: 'mode_marca_id', referencedColumnName: 'marc_id')]
    private ?Marca $marca = null;

    #[ORM\Column(name: 'mode_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}