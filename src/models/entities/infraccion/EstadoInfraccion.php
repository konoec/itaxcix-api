<?php

namespace itaxcix\models\entities\infraccion;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_estado_infraccion')]
class EstadoInfraccion {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'esta_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'esta_nombre', type: 'string', length: 100, unique: true)]
    private string $nombre;

    #[ORM\Column(name: 'esta_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}