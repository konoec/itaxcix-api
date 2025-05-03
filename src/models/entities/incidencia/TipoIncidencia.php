<?php

namespace itaxcix\model\entities\incidencia;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_tipo_incidencia')]
class TipoIncidencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'tipo_id')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, name: 'tipo_nombre')]
    private string $nombre;

    #[ORM\Column(type: 'boolean', name: 'tipo_activo', options: ['default' => true])]
    private bool $activo = true;
}