<?php

namespace itaxcix\model\entities\usuario;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_perfil_conductor')]
class PerfilConductor {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'perf_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'perf_usuario_id', referencedColumnName: 'usua_id')]
    private ?Usuario $usuario = null;

    #[ORM\Column(name: 'perf_area', type: 'string', length: 100)]
    private ?string $area = null;

    #[ORM\Column(name: 'perf_cargo', type: 'string', length: 100)]
    private ?string $cargo = null;
}