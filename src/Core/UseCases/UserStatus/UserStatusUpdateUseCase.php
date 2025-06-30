<?php

namespace itaxcix\Core\UseCases\UserStatus;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\UserStatusModel;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusRequestDTO;

class UserStatusUpdateUseCase
{
    private UserStatusRepositoryInterface $repository;

    public function __construct(UserStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, UserStatusRequestDTO $request): array
    {
        $existingUserStatus = $this->repository->findById($id);
        if (!$existingUserStatus) {
            throw new InvalidArgumentException('Estado de usuario no encontrado');
        }

        // Validar que el nombre no exista en otro registro
        if ($this->repository->existsByName($request->getName(), $id)) {
            throw new InvalidArgumentException('Ya existe otro estado de usuario con este nombre');
        }

        $userStatus = new UserStatusModel(
            $id,
            $request->getName(),
            $request->isActive()
        );

        $updatedUserStatus = $this->repository->update($userStatus);

        return [
            'id' => $updatedUserStatus->getId(),
            'name' => $updatedUserStatus->getName(),
            'active' => $updatedUserStatus->isActive()
        ];
    }
}
