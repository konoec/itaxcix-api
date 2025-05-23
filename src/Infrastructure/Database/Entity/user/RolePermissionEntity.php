<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineRolePermissionRepository;

#[ORM\Entity(repositoryClass: DoctrineRolePermissionRepository::class)]
#[ORM\Table(name: 'tb_rol_permiso')]
class RolePermissionEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rolp_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: RoleEntity::class)]
    #[ORM\JoinColumn(name: 'rolp_rol_id', referencedColumnName: 'rol_id')]
    private ?RoleEntity $role = null;
    #[ORM\ManyToOne(targetEntity: PermissionEntity::class)]
    #[ORM\JoinColumn(name: 'rolp_permiso_id', referencedColumnName: 'perm_id')]
    private ?PermissionEntity $permission = null;
    #[ORM\Column(name: 'rolp_activo', type: 'boolean', options: ['default' => true])]
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

    public function getPermission(): ?PermissionEntity
    {
        return $this->permission;
    }

    public function setPermission(?PermissionEntity $permission): void
    {
        $this->permission = $permission;
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