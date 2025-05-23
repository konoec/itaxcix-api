<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_vehiculo_usuario')]
class VehicleUserEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cont_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'cont_usuario_id', referencedColumnName: 'usua_id', nullable: false)]
    private ?UserEntity $user = null;
    #[ORM\ManyToOne(targetEntity: VehicleEntity::class, inversedBy: 'vehicleUsers')]
    #[ORM\JoinColumn(name: 'vehi_vehiculo_id', referencedColumnName: 'vehi_id', nullable: false)]
    private ?VehicleEntity $vehicle = null;
    #[ORM\Column(name: 'cont_activo', type: 'boolean', nullable: false, options: ['default' => true])]
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

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    public function getVehicle(): ?VehicleEntity
    {
        return $this->vehicle;
    }

    public function setVehicle(?VehicleEntity $vehicle): void
    {
        $this->vehicle = $vehicle;
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