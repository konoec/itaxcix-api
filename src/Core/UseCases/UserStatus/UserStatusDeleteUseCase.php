<?php

namespace itaxcix\Core\UseCases\UserStatus;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;

class UserStatusDeleteUseCase
{
    private UserStatusRepositoryInterface $repository;
    private UserRepositoryInterface $userRepository;

    public function __construct(UserStatusRepositoryInterface $repository, UserRepositoryInterface $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function execute(int $id): bool
    {
        $existingUserStatus = $this->repository->findById($id);
        if (!$existingUserStatus) {
            throw new InvalidArgumentException('Estado de usuario no encontrado');
        }

        if ($existingUserStatus->getName() === 'ACTIVO') {
            throw new InvalidArgumentException('No se puede eliminar el estado ACTIVO de usuario.');
        }

        if ($existingUserStatus->getName() === 'INACTIVO') {
            throw new InvalidArgumentException('No se puede eliminar el estado INACTIVO de usuario.');
        }

        if ($existingUserStatus->getName() === 'BLOQUEADO') {
            throw new InvalidArgumentException('No se puede eliminar el estado BLOQUEADO de usuario.');
        }

        // Verificar si el estado de usuario está asociado a algún usuario
        $usersWithStatus = $this->userRepository->findByStatusId($id);
        if (!empty($usersWithStatus)) {
            throw new InvalidArgumentException('No se puede eliminar el estado de usuario porque está asociado a uno o más usuarios.');
        }

        return $this->repository->delete($id);
    }
}
