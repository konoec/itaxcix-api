<?php

namespace itaxcix\model\entities\tuc;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_estado_tuc')]
class EstadoTuc {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'esta_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'esta_nombre', type: 'string', length: 50)]
    private string $nombre;

    #[ORM\Column(name: 'esta_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}