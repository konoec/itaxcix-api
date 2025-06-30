<?php

namespace itaxcix\Core\UseCases\UserCodeType;

use itaxcix\Core\Domain\user\UserCodeTypeModel;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeResponseDTO;

class UserCodeTypeCreateUseCase
{
    private UserCodeTypeRepositoryInterface $repository;

    public function __construct(UserCodeTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UserCodeTypeRequestDTO $request): UserCodeTypeResponseDTO
    {
        $userCodeType = new UserCodeTypeModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $saved = $this->repository->saveUserCodeType($userCodeType);

        return new UserCodeTypeResponseDTO(
            id: $saved->getId(),
            name: $saved->getName(),
            active: $saved->isActive()
        );
    }
}

