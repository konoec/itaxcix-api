<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_marca')]
class Marca {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'marc_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'marc_nombre', type: 'string', length: 50)]
    private string $nombre;

    #[ORM\Column(name: 'marc_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}