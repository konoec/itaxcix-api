<?php

namespace itaxcix\models\entities\infraccion;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\models\entities\usuario\Usuario;

#[ORM\Entity]
#[ORM\Table(name: 'tb_infraccion')]
class Infraccion {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'infr_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'infr_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: GravedadInfraccion::class)]
    #[ORM\JoinColumn(name: 'infr_gravedad_id', referencedColumnName: 'grav_id')]
    private ?GravedadInfraccion $gravedad = null;

    #[ORM\Column(name: 'infr_fecha', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fecha;

    #[ORM\Column(name: 'infr_descripcion', type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\ManyToOne(targetEntity: EstadoInfraccion::class)]
    #[ORM\JoinColumn(name: 'infr_estado_id', referencedColumnName: 'esta_id')]
    private ?EstadoInfraccion $estado = null;
}