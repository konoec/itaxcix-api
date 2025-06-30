<?php

namespace itaxcix\Core\Handler\Auth;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Domain\user\UserCodeModel;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\UseCases\Auth\StartPasswordRecoveryUseCase;
use itaxcix\Infrastructure\Notifications\NotificationServiceFactory;
use itaxcix\Shared\DTO\useCases\Auth\RecoveryStartRequestDTO;

class StartPasswordRecoveryUseCaseHandler implements StartPasswordRecoveryUseCase
{
    private ContactTypeRepositoryInterface $contactTypeRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private UserCodeRepositoryInterface $userCodeRepository;
    private UserCodeTypeRepositoryInterface $userCodeTypeRepository;
    private NotificationServiceFactory $notificationServiceFactory;
    public function __construct(ContactTypeRepositoryInterface $contactTypeRepository, UserContactRepositoryInterface $userContactRepository, UserCodeRepositoryInterface $userCodeRepository, UserCodeTypeRepositoryInterface $userCodeTypeRepository, NotificationServiceFactory $notificationServiceFactory){
        $this->contactTypeRepository = $contactTypeRepository;
        $this->userContactRepository = $userContactRepository;
        $this->userCodeRepository = $userCodeRepository;
        $this->userCodeTypeRepository = $userCodeTypeRepository;
        $this->notificationServiceFactory = $notificationServiceFactory;
    }

    public function execute(RecoveryStartRequestDTO $dto): ?array
    {
        $contactType = $this->contactTypeRepository->findContactTypeById($dto->contactTypeId);

        if (!$contactType) {
            throw new InvalidArgumentException('El tipo de contacto no existe.');
        }

        $userContact = $this->userContactRepository->findAllUserContactByValue($dto->contactValue);

        if (!$userContact) {
            throw new InvalidArgumentException('El contacto no es válido.');
        }

        if ($userContact->getType()->getId() !== $dto->contactTypeId) {
            throw new InvalidArgumentException('El tipo de contacto no coincide.');
        }

        if (!$userContact->isActive()){
            throw new InvalidArgumentException('El contacto no está activo.');
        }

        $userCodeType = $this->userCodeTypeRepository->findUserCodeTypeByName('RECUPERACIÓN');

        if (!$userCodeType) {
            throw new InvalidArgumentException('El tipo de código de usuario no existe.');
        }

        // Verificar si ya existe un código de recuperación activo (no expirado)
        $preUserCode = $this->userCodeRepository->findUserCodeByUserIdAndTypeId(
            $userContact->getUser()->getId(),
            $userCodeType->getId()
        );
        if ($preUserCode && $preUserCode->getExpirationDate() > new DateTime()) {
            $now = new DateTime();
            $interval = $now->diff($preUserCode->getExpirationDate());
            $minutes = $interval->i + ($interval->h * 60);
            $seconds = $interval->s;
            throw new InvalidArgumentException(
                'Ya se ha enviado un código de recuperación recientemente. Por favor, espere a que expire. Tiempo restante: ' . $minutes . ' minutos y ' . $seconds . ' segundos.'
            );
        }

        $newUserCode = new UserCodeModel(
            id: null,
            type: $userCodeType,
            contact: $userContact,
            code: $this->generateUserCode(),
            expirationDate: (new DateTime())->modify('+5 minutes'),
            useDate: null,
            used: false
        );

        $newUserCode = $this->userCodeRepository->saveUserCode($newUserCode);

        // Enviar correo de confirmación, aquí va el servicio
        $service = $this->notificationServiceFactory->getServiceForContactType($dto->contactTypeId);
        $service->send($newUserCode->getContact()->getValue(), 'Código de recuperación', $newUserCode->getCode());

        return ['userId' => $newUserCode->getContact()->getUser()->getId()];
    }

    private function generateUserCode(int $length = 6): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }
}