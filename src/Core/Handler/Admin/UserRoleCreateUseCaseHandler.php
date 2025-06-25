<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\UserRoleModel;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\UseCases\Admin\UserRoleCreateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleResponseDTO;

class UserRoleCreateUseCaseHandler implements UserRoleCreateUseCase
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

    public function execute(UserRoleCreateRequestDTO $dto): ?UserRoleResponseDTO
    {
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

        // Verificar si ya existe la asignación
        $existingUserRole = $this->userRoleRepository->findByUserAndRole($dto->userId, $dto->roleId);
        if ($existingUserRole) {
            throw new InvalidArgumentException('El rol ya está asignado a este usuario.');
        }

        // Crear nueva asignación
        $userRole = new UserRoleModel(
            id: null,
            role: $role,
            user: $user,
            active: $dto->active
        );

        // Guardar la asignación
        $savedUserRole = $this->userRoleRepository->saveUserRole($userRole);

        // Crear y retornar el DTO de respuesta
        return new UserRoleResponseDTO(
            id: $savedUserRole->getId(),
            userId: $savedUserRole->getUser()->getId(),
            roleId: $savedUserRole->getRole()->getId(),
            active: $savedUserRole->isActive()
        );
    }
}
