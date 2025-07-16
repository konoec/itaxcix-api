<?php

namespace itaxcix\Core\UseCases\UserCodeType;

use itaxcix\Core\Domain\user\UserCodeTypeModel;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeResponseDTO;
class UserCodeTypeUpdateUseCase
{
    private UserCodeTypeRepositoryInterface $repository;

    public function __construct(UserCodeTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, UserCodeTypeRequestDTO $request): UserCodeTypeResponseDTO
    {
        $userCodeType = new UserCodeTypeModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updated = $this->repository->saveUserCodeType($userCodeType);

        return new UserCodeTypeResponseDTO(
            id: $updated->getId(),
            name: $updated->getName(),
            active: $updated->isActive()
        );
    }
}

