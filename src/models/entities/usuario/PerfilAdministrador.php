<?php

namespace itaxcix\models\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_perfil_administrador')]
class PerfilAdministrador {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'perf_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'perf_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\Column(name: 'perf_disponible', type: 'boolean', options: ['default' => false])]
    private bool $disponible = false;
}