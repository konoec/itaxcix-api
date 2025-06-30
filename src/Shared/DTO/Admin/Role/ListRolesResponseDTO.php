<?php

namespace itaxcix\Shared\DTO\Admin\Role;

readonly class ListRolesResponseDTO
{
    public array $roles;
    public function __construct(
        array $roles,
        public int $total,
        public int $page,
        public int $limit,
        public int $totalPages
    ) {
        // Convertir cada RoleModel a array si es necesario
        $this->roles = array_map(function($role) {
            return method_exists($role, 'getId') ? [
                'id' => $role->getId(),
                'name' => $role->getName(),
                'active' => $role->isActive(),
                'web' => $role->isWeb(),
            ] : $role;
        }, $roles);
    }
}
