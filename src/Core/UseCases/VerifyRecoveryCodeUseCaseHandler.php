<?php

namespace itaxcix\Core\UseCases;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Infrastructure\Auth\Service\JwtService;
use itaxcix\Shared\DTO\useCases\VerificationCodeRequestDTO;

class VerifyRecoveryCodeUseCaseHandler implements VerifyRecoveryCodeUseCase {
    private UserCodeRepositoryInterface $userCodeRepository;
    private JwtService $jwtService;

    public function __construct(UserCodeRepositoryInterface $userCodeRepository, JwtService $jwtService)
    {
        $this->userCodeRepository = $userCodeRepository;
        $this->jwtService = $jwtService;
    }
    public function execute(VerificationCodeRequestDTO $dto): ?array
    {
        $userCode = $this->userCodeRepository->findUserCodeByValue($dto->code);

        if (!$userCode) {
            throw new InvalidArgumentException('El código de recuperación no es válido.');
        }

        if ($userCode->getContact()->getUser()->getId() !== $dto->userId) {
            throw new InvalidArgumentException('El código de recuperación no es válido para este usuario.');
        }

        if ($userCode->getExpirationDate() < new DateTime()) {
            throw new InvalidArgumentException('El código de recuperación ha expirado.');
        }

        if ($userCode->isUsed()) {
            throw new InvalidArgumentException('El código de recuperación ya ha sido utilizado.');
        }

        $userCode->setUsed(true);
        $this->userCodeRepository->saveUserCode($userCode);

        $token = $this->jwtService->encode(['userId' => $dto->userId]);

        return [
            'message' => 'El código de recuperación es válido.',
            'token'   => $token
        ];
    }
}