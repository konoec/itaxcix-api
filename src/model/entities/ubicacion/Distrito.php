<?php

namespace itaxcix\model\entities\ubicacion;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_distrito')]
class Distrito {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'dist_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'dist_nombre', type: 'string', length: 50, nullable: true)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(targetEntity: Provincia::class)]
    #[ORM\JoinColumn(name: 'dist_provincia_id', referencedColumnName: 'prov_id')]
    private ?Provincia $provincia = null;

    #[ORM\Column(name: 'dist_ubigeo', type: 'string', length: 6, nullable: true)]
    private ?string $ubigeo = null;
}