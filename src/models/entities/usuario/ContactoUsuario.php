<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_contacto_usuario')]
class ContactoUsuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cont_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'cont_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: TipoContacto::class)]
    #[ORM\JoinColumn(name: 'cont_tipo_id', referencedColumnName: 'tipo_id')]
    private ?TipoContacto $tipo = null;

    #[ORM\Column(name: 'cont_valor', type: 'string', length: 100)]
    private string $valor;

    #[ORM\Column(name: 'cont_confirmado', type: 'boolean')]
    private bool $confirmado;

    #[ORM\Column(name: 'cont_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}