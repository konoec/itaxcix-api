<?php

namespace itaxcix\Core\UseCases;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
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

        $user->setPassword($dto->newPassword);
        $this->userRepository->saveUser($user);

        return [
            'message' => 'Contraseña cambiada exitosamente'
        ];
    }
}