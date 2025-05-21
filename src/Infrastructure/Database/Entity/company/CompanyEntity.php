<?php

namespace itaxcix\Infrastructure\Database\Entity\company;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_empresa')]
class CompanyEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'empr_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'empr_ruc', type: 'string', length: 11, unique: true, nullable: true)]
    private ?string $ruc = null;
    #[ORM\Column(name: 'empr_nombre', type: 'string', length: 100, nullable: false)]
    private string $name;
    #[ORM\Column(name: 'empr_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}