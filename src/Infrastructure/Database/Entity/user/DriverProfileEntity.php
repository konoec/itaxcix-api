<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_perfil_conductor')]
class DriverProfileEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'perf_id', type: 'integer')]
    private int $id;
    #[ORM\OneToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'perf_usuario_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $user = null;
    #[ORM\Column(name: 'perf_disponible', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $available = false;

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

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }
}