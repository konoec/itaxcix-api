<?php

namespace itaxcix\Core\Handler\Profile;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Domain\user\UserCodeModel;
use itaxcix\Core\Domain\user\UserContactModel;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\UseCases\Profile\ChangePhoneUseCase;
use itaxcix\Infrastructure\Notifications\NotificationServiceFactory;
use itaxcix\Shared\DTO\useCases\Profile\ChangePhoneRequestDTO;

class ChangePhoneUseCaseHandler implements ChangePhoneUseCase
{
    private UserRepositoryInterface $userRepository;
    private ContactTypeRepositoryInterface $contactTypeRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private UserCodeTypeRepositoryInterface $userCodeTypeRepository;
    private UserCodeRepositoryInterface $userCodeRepository;
    private NotificationServiceFactory $notificationServiceFactory;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ContactTypeRepositoryInterface $contactTypeRepository,
        UserContactRepositoryInterface $userContactRepository,
        UserCodeTypeRepositoryInterface $userCodeTypeRepository,
        UserCodeRepositoryInterface $userCodeRepository,
        NotificationServiceFactory $notificationServiceFactory
    ) {
        $this->userRepository = $userRepository;
        $this->contactTypeRepository = $contactTypeRepository;
        $this->userContactRepository = $userContactRepository;
        $this->userCodeTypeRepository = $userCodeTypeRepository;
        $this->userCodeRepository = $userCodeRepository;
        $this->notificationServiceFactory = $notificationServiceFactory;
    }

    public function execute(ChangePhoneRequestDTO $dto): ?array
    {
        // Verificar si existe usuario
        $user = $this->userRepository->findUserById($dto->userId);
        if (!$user) {
            throw new InvalidArgumentException('Usuario no encontrado.');
        }

        // Verificar que el nuevo teléfono no esté registrado
        $existingContact = $this->userContactRepository->findAllUserContactByValue($dto->phone);
        if ($existingContact) {
            throw new InvalidArgumentException('El número telefónico ya está registrado.');
        }

        // Obtener tipo de contacto para teléfono
        $contactType = $this->contactTypeRepository->findContactTypeByName('TELÉFONO MÓVIL');
        if (!$contactType) {
            throw new InvalidArgumentException('Tipo de contacto no encontrado.');
        }

        // Crear o actualizar contacto de teléfono
        $userContact = $this->userContactRepository->findUserContactByTypeAndUser(
            $contactType->getId(),
            $user->getId()
        );

        if ($userContact) {
            // Si ya existe un contacto de teléfono, lo actualizamos
            $userContact->setValue($dto->phone);
            $userContact->setConfirmed(false);
        } else {
            // Si no existe, creamos uno nuevo
            $userContact = new UserContactModel(
                id: null,
                user: $user,
                type: $contactType,
                value: $dto->phone,
                confirmed: false,
                active: true
            );
        }

        $userContact = $this->userContactRepository->saveUserContact($userContact);

        // Obtener tipo de código para verificación
        $userCodeType = $this->userCodeTypeRepository->findUserCodeTypeByName('VERIFICACIÓN');
        if (!$userCodeType) {
            throw new InvalidArgumentException('Tipo de código no encontrado.');
        }

        // Generar código de verificación
        $userCode = new UserCodeModel(
            id: null,
            type: $userCodeType,
            contact: $userContact,
            code: $this->generateUserCode(),
            expirationDate: (new DateTime())->modify('+5 minutes'),
            useDate: null,
            used: false
        );

        $userCode = $this->userCodeRepository->saveUserCode($userCode);

        // Enviar código por SMS
        $notificationService = $this->notificationServiceFactory->getServiceForContactType($contactType->getId());
        $notificationService->send(
            $userContact->getValue(),
            'Código de verificación',
            $userCode->getCode(),
            'verification'
        );

        return [
            'message' => 'Se ha enviado un código de verificación a tu nuevo número telefónico.'
        ];
    }

    private function generateUserCode(int $length = 6): string
    {
        $characters = '0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }
}
