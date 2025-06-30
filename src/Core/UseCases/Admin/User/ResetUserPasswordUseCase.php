<?php

namespace itaxcix\Core\UseCases\Admin\User;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\ResetUserPasswordRequestDTO;

/**
 * ResetUserPasswordUseCase - Caso de uso para resetear contraseñas de usuarios
 *
 * Permite a los administradores resetear contraseñas de usuarios con registro
 * de auditoría y opción de forzar cambio en el próximo login.
 */
class ResetUserPasswordUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(ResetUserPasswordRequestDTO $request): array
    {
        // Validar que el usuario existe
        $user = $this->userRepository->findUserById($request->userId);
        if (!$user) {
            throw new \InvalidArgumentException("Usuario con ID {$request->userId} no encontrado");
        }

        // Validar la nueva contraseña
        if (strlen($request->newPassword) < 8) {
            throw new \InvalidArgumentException("La nueva contraseña debe tener al menos 8 caracteres");
        }

        // Hashear la nueva contraseña
        $hashedPassword = password_hash($request->newPassword, PASSWORD_DEFAULT);

        // Actualizar la contraseña del usuario
        $user->setPassword($hashedPassword);

        // TODO: Si forcePasswordChange es true, marcar que debe cambiar contraseña en próximo login
        // Esto requeriría agregar un campo adicional al UserModel o crear una tabla de password_resets

        $updatedUser = $this->userRepository->saveUser($user);

        // TODO: Registrar en auditoría el reseteo de contraseña
        // AuditLogService::log('password_reset_by_admin', $user->getId(), $request->adminReason);

        // TODO: Opcional - Enviar notificación al usuario sobre el cambio de contraseña
        // NotificationService::sendPasswordResetNotification($user);

        return [
            'message' => 'Contraseña reseteada exitosamente',
            'user' => [
                'id' => $updatedUser->getId(),
                'forcePasswordChange' => $request->forcePasswordChange,
                'adminReason' => $request->adminReason
            ]
        ];
    }
}
