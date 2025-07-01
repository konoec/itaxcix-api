<?php

namespace itaxcix\Core\UseCases\Admin\User;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\ChangeUserStatusRequestDTO;

/**
 * ChangeUserStatusUseCase - Caso de uso para cambiar el estado de un usuario
 *
 * Permite a los administradores cambiar el estado de un usuario
 * (ACTIVO, SUSPENDIDO, BANEADO, etc.) con registro de auditoría.
 */
class ChangeUserStatusUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserStatusRepositoryInterface $userStatusRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserStatusRepositoryInterface $userStatusRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userStatusRepository = $userStatusRepository;
    }

    public function execute(ChangeUserStatusRequestDTO $request): array
    {
        // Validar que el usuario existe
        $user = $this->userRepository->findUserById($request->userId);
        if (!$user) {
            throw new \InvalidArgumentException("Usuario con ID {$request->userId} no encontrado");
        }

        // Validar que el estado existe
        $newStatus = $this->userStatusRepository->findUserStatusById($request->statusId);
        if (!$newStatus) {
            throw new \InvalidArgumentException("Estado de usuario con ID {$request->statusId} no encontrado");
        }

        // Verificar si el estado es diferente al actual
        $currentStatus = $user->getStatus();
        if ($currentStatus->getId() === $newStatus->getId()) {
            throw new \InvalidArgumentException("El usuario ya tiene el estado: {$newStatus->getName()}");
        }

        // Actualizar el estado del usuario
        $user->setStatus($newStatus);
        $updatedUser = $this->userRepository->saveUser($user);

        // TODO: Registrar en auditoría el cambio de estado con la razón
        // AuditLogService::log('user_status_changed', $user->getId(), $request->reason);

        return [
            'message' => 'Estado de usuario actualizado exitosamente',
            'user' => [
                'id' => $updatedUser->getId(),
                'previousStatus' => $currentStatus->getName(),
                'newStatus' => $newStatus->getName()
            ]
        ];
    }
}
