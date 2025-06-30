<?php

namespace itaxcix\Core\UseCases\UserStatus;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;

class UserStatusDeleteUseCase
{
    private UserStatusRepositoryInterface $repository;

    public function __construct(UserStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        $existingUserStatus = $this->repository->findById($id);
        if (!$existingUserStatus) {
            throw new InvalidArgumentException('Estado de usuario no encontrado');
        }

        return $this->repository->delete($id);
    }
}
