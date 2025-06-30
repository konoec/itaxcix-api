<?php

namespace itaxcix\Core\UseCases\Admin\User;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\GetUserWithRolesRequestDTO;
use itaxcix\Shared\DTO\Admin\User\GetUserWithRolesResponseDTO;
use InvalidArgumentException;

class GetUserWithRolesUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(GetUserWithRolesRequestDTO $request): GetUserWithRolesResponseDTO
    {
        // Verificar que el usuario existe
        $user = $this->userRepository->findById($request->userId);
        if ($user === null) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }

        // Obtener todos los roles asignados al usuario
        $roles = $this->userRoleRepository->findActiveRolesByUserId($request->userId);

        return new GetUserWithRolesResponseDTO(
            userId: $user->getId(),
            userName: $user->getPerson()->getName(),
            userLastName: $user->getPerson()->getLastName(),
            userDocument: $user->getPerson()->getDocument(),
            userEmail: $user->getPerson()->getEmail(),
            roles: $roles
        );
    }
}
