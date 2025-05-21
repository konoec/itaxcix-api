<?php

namespace itaxcix\Infrastructure\Database\Entity\incident;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_tipo_incidencia')]
class IncidentTypeEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tipo_id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'tipo_nombre', type: 'string', length: 100)]
    private string $name;
    #[ORM\Column(name: 'tipo_activo', type: 'boolean', options: ['default' => true])]
    private bool $active = true;
}