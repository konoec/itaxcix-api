<?php

namespace itaxcix\models\entities\ubicacion;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_provincia')]
class Provincia {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'prov_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'prov_nombre', type: 'string', length: 50, nullable: true)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(targetEntity: Departamento::class)]
    #[ORM\JoinColumn(name: 'prov_departamento_id', referencedColumnName: 'depa_id')]
    private ?Departamento $departamento = null;

    #[ORM\Column(name: 'prov_ubigeo', type: 'string', length: 6, nullable: true)]
    private ?string $ubigeo = null;
}