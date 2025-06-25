<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\UseCases\Admin\RoleDeleteUseCase;
use itaxcix\Shared\DTO\useCases\Admin\RoleDeleteRequestDTO;

class RoleDeleteUseCaseHandler implements RoleDeleteUseCase
{
    private RoleRepositoryInterface $roleRepository;
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(RoleDeleteRequestDTO $dto): void
    {
        // Verificar si el rol existe
        $role = $this->roleRepository->findRoleById($dto->id);
        if (!$role) {
            throw new InvalidArgumentException('El rol no existe.');
        }

        // Verificar si el rol tiene usuarios asignados
        $hasUsers = $this->userRoleRepository->hasActiveUsersByRoleId($dto->id);
        if ($hasUsers) {
            throw new InvalidArgumentException('No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        // Eliminar el rol
        $this->roleRepository->deleteRole($role);
    }
}
