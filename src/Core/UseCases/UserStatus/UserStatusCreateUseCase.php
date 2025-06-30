<?php

namespace itaxcix\Core\UseCases\UserStatus;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\UserStatusModel;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusRequestDTO;

class UserStatusCreateUseCase
{
    private UserStatusRepositoryInterface $repository;

    public function __construct(UserStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UserStatusRequestDTO $request): array
    {
        // Validar que el nombre no exista
        if ($this->repository->existsByName($request->getName())) {
            throw new InvalidArgumentException('Ya existe un estado de usuario con este nombre');
        }

        $userStatus = new UserStatusModel(
            null,
            $request->getName(),
            $request->isActive()
        );

        $createdUserStatus = $this->repository->create($userStatus);

        return [
            'id' => $createdUserStatus->getId(),
            'name' => $createdUserStatus->getName(),
            'active' => $createdUserStatus->isActive()
        ];
    }
}
