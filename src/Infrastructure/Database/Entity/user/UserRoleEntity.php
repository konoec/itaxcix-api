<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserRoleRepository;

#[ORM\Entity(repositoryClass: DoctrineUserRoleRepository::class)]
#[ORM\Table(name: 'tb_rol_usuario')]
class UserRoleEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rolu_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: RoleEntity::class)]
    #[ORM\JoinColumn(name: 'rolu_rol_id', referencedColumnName: 'rol_id', nullable: false)]
    private ?RoleEntity $role = null;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'rolu_usuario_id', referencedColumnName: 'usua_id', nullable: false)]
    private ?UserEntity $user = null;
    #[ORM\Column(name: 'rolu_activo', type: 'boolean', nullable: false, options: ['default' => true])]
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

    public function getRole(): ?RoleEntity
    {
        return $this->role;
    }

    public function setRole(?RoleEntity $role): void
    {
        $this->role = $role;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
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