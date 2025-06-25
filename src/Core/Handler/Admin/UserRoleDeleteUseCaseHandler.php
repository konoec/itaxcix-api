<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\UseCases\Admin\UserRoleDeleteUseCase;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleDeleteRequestDTO;

class UserRoleDeleteUseCaseHandler implements UserRoleDeleteUseCase
{
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(UserRoleRepositoryInterface $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(UserRoleDeleteRequestDTO $dto): void
    {
        // Verificar si existe la asignación
        $userRole = $this->userRoleRepository->findUserRoleById($dto->id);
        if (!$userRole) {
            throw new InvalidArgumentException('La asignación de rol al usuario no existe.');
        }

        // Eliminar la asignación
        $this->userRoleRepository->deleteUserRole($userRole);
    }
}
