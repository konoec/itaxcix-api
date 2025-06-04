<?php

namespace itaxcix\Core\Handler;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\UseCases\ChangePasswordUseCase;
use itaxcix\Shared\DTO\useCases\PasswordChangeRequestDTO;

class ChangePasswordUseCaseHandler implements ChangePasswordUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(PasswordChangeRequestDTO $dto): ?array
    {
        $user = $this->userRepository->findUserById($dto->userId);

        if (!$user) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }

        $user->setPassword(password_hash($dto->newPassword, PASSWORD_DEFAULT));
        $this->userRepository->saveUser($user);

        return [
            'message' => 'Contraseña cambiada exitosamente'
        ];
    }
}