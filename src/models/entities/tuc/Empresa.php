<?php

namespace itaxcix\models\entities\tuc;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_empresa')]
class Empresa {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'empr_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'empr_ruc', type: 'string', length: 11, nullable: true)]
    private ?string $ruc = null;

    #[ORM\Column(name: 'empr_nombre', type: 'string', length: 100)]
    private string $nombre;

    #[ORM\Column(name: 'empr_activo', type: 'boolean', options: ['default' => true])]
    private bool $activo = true;
}