<?php

namespace itaxcix\models\entities\vehiculo;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_color')]
class Color {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'colo_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'colo_nombre', type: 'string', length: 50)]
    private string $nombre;

    #[ORM\Column(name: 'colo_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}