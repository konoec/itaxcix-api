<?php

namespace itaxcix\Core\Handler;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\UseCases\VerificationCodeUseCase;
use itaxcix\Shared\DTO\useCases\VerificationCodeRequestDTO;

class VerificationCodeUseCaseHandler implements VerificationCodeUseCase {
    private UserCodeRepositoryInterface $userCodeRepository;
    private UserContactRepositoryInterface $userContactRepository;

    public function __construct(UserCodeRepositoryInterface $userCodeRepository, UserContactRepositoryInterface $userContactRepository)
    {
        $this->userCodeRepository = $userCodeRepository;
        $this->userContactRepository = $userContactRepository;
    }

    public function execute(VerificationCodeRequestDTO $dto): ?array
    {
        $userCode = $this->userCodeRepository
            ->findUserCodeByValueAndUser($dto->code, $dto->userId);

        if (!$userCode) {
            throw new InvalidArgumentException('El código de verificación no es válido.');
        }

        if ($userCode->getExpirationDate() < new DateTime()) {
            throw new InvalidArgumentException('El código de verificación ha expirado.');
        }

        $userCode->setUsed(true);
        $this->userCodeRepository->saveUserCode($userCode);

        $userContact = $userCode->getContact();
        $userContact->setConfirmed(true);
        $this->userContactRepository->saveUserContact($userContact);

        return [
            'message' => 'El código de verificación es válido.'
        ];
    }
}