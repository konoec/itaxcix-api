<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_ruta_servicio')]
class ServiceRouteEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ruta_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: TucProcedureEntity::class)]
    #[ORM\JoinColumn(name: 'ruta_tram_id', referencedColumnName: 'tram_id')]
    private ?TucProcedureEntity $procedure = null;
    #[ORM\ManyToOne(targetEntity: ServiceTypeEntity::class)]
    #[ORM\JoinColumn(name: 'ruta_tipo_id', referencedColumnName: 'tipo_id')]
    private ?ServiceTypeEntity $serviceType = null;
    #[ORM\Column(name: 'ruta_texto', type: 'text', nullable: true)]
    private ?string $text = null;
    #[ORM\Column(name: 'ruta_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}