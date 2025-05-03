<?php

namespace itaxcix\model\entities\ubicacion;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_departamento')]
class Departamento {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'depa_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'depa_nombre', type: 'string', length: 50, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(name: 'depa_ubigeo', type: 'string', length: 6, nullable: true)]
    private ?string $ubigeo = null;
}