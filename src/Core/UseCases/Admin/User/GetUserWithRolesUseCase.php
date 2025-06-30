<?php

namespace itaxcix\Core\UseCases\Admin\User;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\GetUserWithRolesRequestDTO;
use itaxcix\Shared\DTO\Admin\User\GetUserWithRolesResponseDTO;
use InvalidArgumentException;

class GetUserWithRolesUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    private UserContactRepositoryInterface $userContactRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserRoleRepositoryInterface $userRoleRepository,
        UserContactRepositoryInterface $userContactRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userContactRepository = $userContactRepository;
    }

    public function execute(GetUserWithRolesRequestDTO $request): GetUserWithRolesResponseDTO
    {
        // Verificar que el usuario existe
        $user = $this->userRepository->findById($request->userId);
        if ($user === null) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }

        // Obtener todos los roles asignados al usuario
        $userRoles = $this->userRoleRepository->findActiveRolesByUserId($request->userId);

        // Mapear los roles a un array asociativo para la respuesta
        $roles = array_map(function($userRole) {
            $role = $userRole->getRole();
            return [
                'id' => $role->getId(),
                'name' => $role->getName(),
                'active' => $role->isActive(),
                'web' => $role->isWeb(),
            ];
        }, $userRoles);

        // Obtener el email del usuario desde UserContact (tipo 1)
        $emailContact = $this->userContactRepository->findActiveContactByUserAndType($user->getId(), 1);
        $userEmail = $emailContact?->getValue();

        return new GetUserWithRolesResponseDTO(
            userId: $user->getId(),
            userName: $user->getPerson()->getName(),
            userLastName: $user->getPerson()->getLastName(),
            userDocument: $user->getPerson()->getDocument(),
            userEmail: $userEmail,
            roles: $roles
        );
    }
}
