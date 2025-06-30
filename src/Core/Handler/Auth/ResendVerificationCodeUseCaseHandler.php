<?php

namespace itaxcix\Core\Handler\Auth;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Domain\user\UserCodeModel;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\UseCases\Auth\ResendVerificationCodeUseCase as ResendInterface;
use itaxcix\Infrastructure\Notifications\NotificationServiceFactory;
use itaxcix\Shared\DTO\useCases\Auth\ResendVerificationCodeRequestDTO;

class ResendVerificationCodeUseCaseHandler implements ResendInterface
{
    private UserRepositoryInterface $userRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private UserCodeRepositoryInterface $userCodeRepository;
    private UserCodeTypeRepositoryInterface $userCodeTypeRepository;
    private NotificationServiceFactory $notificationServiceFactory;
    public function __construct(UserRepositoryInterface $userRepository, UserContactRepositoryInterface $userContactRepository, UserCodeRepositoryInterface $userCodeRepository, UserCodeTypeRepositoryInterface $userCodeTypeRepository, NotificationServiceFactory $notificationServiceFactory){
        $this->userRepository = $userRepository;
        $this->userContactRepository = $userContactRepository;
        $this->userCodeRepository = $userCodeRepository;
        $this->notificationServiceFactory = $notificationServiceFactory;
        $this->userCodeTypeRepository = $userCodeTypeRepository;
    }

    /**
     * @throws \Exception
     */
    public function execute(ResendVerificationCodeRequestDTO $dto): array
    {
        $userCodeType = $this->userCodeTypeRepository->findUserCodeTypeByName('VERIFICACIÓN');
        $user = $this->userRepository->findUserById($dto->userId);

        if (!$user) {
            throw new InvalidArgumentException('Usuario no encontrado.');
        }

        $contact = $this->userContactRepository->findUserContactByUserId($user->getId());

        if (!$contact) {
            throw new InvalidArgumentException('El usuario no tiene un contacto asociado.');
        }

        if ($contact->isConfirmed()) {
            throw new InvalidArgumentException('El contacto ya está confirmado.');
        }

        $preUserCode = $this->userCodeRepository->findUserCodeByUserIdAndTypeId($user->getId(), $userCodeType->getId());

        if ($preUserCode && $preUserCode->getExpirationDate() > new DateTime()) {
            $now = new DateTime();
            $interval = $now->diff($preUserCode->getExpirationDate());
            $minutes = $interval->i + ($interval->h * 60);
            $seconds = $interval->s;
            throw new InvalidArgumentException(
                'Ya se ha enviado un código de verificación recientemente. Por favor, espere a que expire. Tiempo restante: ' . $minutes . ' minutos y ' . $seconds . ' segundos.'
            );
        }

        $code = $this->generateUserCode();
        $expires = (new DateTime())->modify('+5 minutes');
        $userCode = new UserCodeModel(
            id: null,
            type: $userCodeType,
            contact: $contact,
            code: $code,
            expirationDate: $expires,
            useDate: null,
            used: false
        );
        $this->userCodeRepository->saveUserCode($userCode);

        $service = $this->notificationServiceFactory
            ->getServiceForContactType($contact->getType()->getId());
        $service->send($contact->getValue(), 'Código de verificación', $code, 'verification');

        return ['message' => 'Código reenviado correctamente'];
    }

    private function generateUserCode(int $length = 6): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }
}