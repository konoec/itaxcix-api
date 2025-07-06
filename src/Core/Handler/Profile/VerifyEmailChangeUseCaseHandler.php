<?php

namespace itaxcix\Core\Handler\Profile;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\UseCases\Profile\VerifyEmailChangeUseCase;
use itaxcix\Shared\DTO\useCases\Profile\VerifyEmailChangeRequestDTO;

class VerifyEmailChangeUseCaseHandler implements VerifyEmailChangeUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserCodeRepositoryInterface $userCodeRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private ContactTypeRepositoryInterface $contactTypeRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserCodeRepositoryInterface $userCodeRepository,
        UserContactRepositoryInterface $userContactRepository,
        ContactTypeRepositoryInterface $contactTypeRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userCodeRepository = $userCodeRepository;
        $this->userContactRepository = $userContactRepository;
        $this->contactTypeRepository = $contactTypeRepository;
    }

    public function execute(VerifyEmailChangeRequestDTO $dto): ?array
    {
        // Verificar si existe usuario
        $user = $this->userRepository->findUserById($dto->userId);
        if (!$user) {
            throw new InvalidArgumentException('Usuario no encontrado.');
        }

        // Obtener tipo de contacto para email
        $contactType = $this->contactTypeRepository->findContactTypeByName('CORREO ELECTRÓNICO');
        if (!$contactType) {
            throw new InvalidArgumentException('Tipo de contacto no encontrado.');
        }

        // Obtener el contacto pendiente de verificación del usuario
        $userContact = $this->userContactRepository->findUserContactByTypeAndUser(
            $contactType->getId(),
            $user->getId()
        );

        if (!$userContact) {
            throw new InvalidArgumentException('No hay cambios de correo pendientes de verificar.');
        }

        if ($userContact->isConfirmed()) {
            throw new InvalidArgumentException('El correo ya fue verificado anteriormente.');
        }

        // Verificar el código
        $userCode = $this->userCodeRepository->findLatestUnusedCodeByContact($userContact->getId());
        if (!$userCode) {
            throw new InvalidArgumentException('No se encontró un código de verificación válido.');
        }

        if ($userCode->getCode() !== $dto->code) {
            throw new InvalidArgumentException('El código de verificación es incorrecto.');
        }

        if ($userCode->getExpirationDate() < new DateTime()) {
            throw new InvalidArgumentException('El código de verificación ha expirado.');
        }

        // Marcar código como usado
        $userCode->setUsed(true);
        $userCode->setUseDate(new DateTime());
        $this->userCodeRepository->saveUserCode($userCode);

        // Desactivar el contacto anterior si existe
        $previousContact = $this->userContactRepository->findConfirmedContactByUserAndType(
            $user->getId(),
            $contactType->getId()
        );
        if ($previousContact && $previousContact->getId() !== $userContact->getId()) {
            $previousContact->setActive(false);
            $this->userContactRepository->saveUserContact($previousContact);
        }

        // Confirmar el nuevo contacto
        $userContact->setConfirmed(true);
        $this->userContactRepository->saveUserContact($userContact);

        return [
            'message' => 'Correo electrónico verificado correctamente.'
        ];
    }
}
