<?php

namespace itaxcix\model\entities\incidencia;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\model\entities\usuario\Usuario;
use itaxcix\model\entities\viaje\Viaje;

#[ORM\Entity]
#[ORM\Table(name: 'tb_incidencia')]
class Incidencia {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'inci_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'inci_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: Viaje::class)]
    #[ORM\JoinColumn(name: 'inci_viaje_id', referencedColumnName: 'viaj_id')]
    private ?Viaje $viaje = null;

    #[ORM\ManyToOne(targetEntity: TipoIncidencia::class)]
    #[ORM\JoinColumn(name: 'inci_tipo_id', referencedColumnName: 'tipo_id')]
    private ?TipoIncidencia $tipo = null;

    #[ORM\Column(name: 'inci_comentario', type: 'string', length: 255, nullable: true)]
    private ?string $comentario = null;

    #[ORM\Column(name: 'inci_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}