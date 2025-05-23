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

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getProcedure(): ?TucProcedureEntity
    {
        return $this->procedure;
    }

    public function setProcedure(?TucProcedureEntity $procedure): void
    {
        $this->procedure = $procedure;
    }

    public function getServiceType(): ?ServiceTypeEntity
    {
        return $this->serviceType;
    }

    public function setServiceType(?ServiceTypeEntity $serviceType): void
    {
        $this->serviceType = $serviceType;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

}