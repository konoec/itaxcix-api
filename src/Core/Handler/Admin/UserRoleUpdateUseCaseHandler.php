<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\UseCases\Admin\UserRoleUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleResponseDTO;

class UserRoleUpdateUseCaseHandler implements UserRoleUpdateUseCase
{
    private UserRepositoryInterface $userRepository;
    private RoleRepositoryInterface $roleRepository;
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(UserRoleUpdateRequestDTO $dto): ?UserRoleResponseDTO
    {
        // Verificar si existe la asignación
        $userRole = $this->userRoleRepository->findUserRoleById($dto->id);
        if (!$userRole) {
            throw new InvalidArgumentException('La asignación de rol al usuario no existe.');
        }

        // Verificar si el usuario existe
        $user = $this->userRepository->findUserById($dto->userId);
        if (!$user) {
            throw new InvalidArgumentException('El usuario no existe.');
        }

        // Verificar si el rol existe
        $role = $this->roleRepository->findRoleById($dto->roleId);
        if (!$role) {
            throw new InvalidArgumentException('El rol no existe.');
        }

        // Verificar si ya existe otra asignación con el mismo usuario y rol
        $existingUserRole = $this->userRoleRepository->findByUserAndRole($dto->userId, $dto->roleId);
        if ($existingUserRole && $existingUserRole->getId() !== $dto->id) {
            throw new InvalidArgumentException('El rol ya está asignado a este usuario.');
        }

        // Actualizar la asignación
        $userRole->setUser($user);
        $userRole->setRole($role);
        $userRole->setActive($dto->active);

        // Guardar los cambios
        $updatedUserRole = $this->userRoleRepository->saveUserRole($userRole);

        // Crear y retornar el DTO de respuesta
        return new UserRoleResponseDTO(
            id: $updatedUserRole->getId(),
            userId: $updatedUserRole->getUser()->getId(),
            roleId: $updatedUserRole->getRole()->getId(),
            active: $updatedUserRole->isActive()
        );
    }
}
