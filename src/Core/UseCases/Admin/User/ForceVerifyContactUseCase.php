<?php

namespace itaxcix\Core\UseCases\Admin\User;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\ForceVerifyContactRequestDTO;

/**
 * ForceVerifyContactUseCase - Caso de uso para verificar contactos manualmente
 *
 * Permite a los administradores verificar contactos (email/teléfono) de usuarios
 * manualmente, bypassing el proceso normal de verificación con códigos.
 * Útil para resolver problemas de usuarios o situaciones especiales.
 */
class ForceVerifyContactUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserContactRepositoryInterface $userContactRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserContactRepositoryInterface $userContactRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userContactRepository = $userContactRepository;
    }

    public function execute(ForceVerifyContactRequestDTO $request): array
    {
        // Validar que el usuario existe
        $user = $this->userRepository->findUserById($request->userId);
        if (!$user) {
            throw new InvalidArgumentException("Usuario con ID {$request->userId} no encontrado");
        }

        // Validar que el contacto existe y pertenece al usuario
        $contact = $this->userContactRepository->findUserContactById($request->contactId);
        if (!$contact) {
            throw new InvalidArgumentException("Contacto con ID {$request->contactId} no encontrado");
        }

        if ($contact->getUser()->getId() !== $user->getId()) {
            throw new InvalidArgumentException("El contacto no pertenece al usuario especificado");
        }

        // Verificar si ya está confirmado
        if ($contact->isConfirmed()) {
            throw new InvalidArgumentException("El contacto ya está verificado");
        }

        // Verificar el contacto manualmente
        $contact->setConfirmed(true);
        $updatedContact = $this->userContactRepository->saveUserContact($contact);

        // TODO: Registrar en auditoría la verificación manual
        // AuditLogService::log('contact_force_verified', $user->getId(), $request->adminReason);

        return [
            'message' => 'Contacto verificado exitosamente por administrador',
            'contact' => [
                'id' => $updatedContact->getId(),
                'type' => $updatedContact->getType()->getName(),
                'value' => $updatedContact->getValue(),
                'confirmed' => $updatedContact->isConfirmed()
            ]
        ];
    }
}
