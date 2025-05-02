<?php

namespace itaxcix\entity\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_tipo_codigo_usuario')]
class TipoCodigoUsuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tipo_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'tipo_nombre', type: 'string', length: 50)]
    private string $nombre;

    #[ORM\Column(name: 'tipo_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}