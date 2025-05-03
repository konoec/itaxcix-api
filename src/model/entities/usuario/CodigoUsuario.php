<?php

namespace itaxcix\model\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_codigo_usuario')]
class CodigoUsuario {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codi_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'codi_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: TipoCodigoUsuario::class)]
    #[ORM\JoinColumn(name: 'codi_tipo_id', referencedColumnName: 'tipo_id')]
    private ?TipoCodigoUsuario $tipo = null;

    #[ORM\ManyToOne(targetEntity: ContactoUsuario::class)]
    #[ORM\JoinColumn(name: 'codi_contacto_id', referencedColumnName: 'cont_id')]
    private ?ContactoUsuario $contacto = null;

    #[ORM\Column(name: 'codi_codigo', type: 'string', length: 8)]
    private string $codigo;

    #[ORM\Column(name: 'codi_fecha_expiracion', type: 'datetime')]
    private \DateTime $fechaExpiracion;

    #[ORM\Column(name: 'codi_fecha_uso', type: 'datetime', nullable: true)]
    private ?\DateTime $fechaUso = null;

    #[ORM\Column(name: 'codi_usado', type: 'boolean', options: ['default' => false])]
    private bool $usado = false;
}