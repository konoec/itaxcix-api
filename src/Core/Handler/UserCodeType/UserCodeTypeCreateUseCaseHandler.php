<?php

namespace itaxcix\Core\Handler\UserCodeType;

use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeCreateUseCase;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeResponseDTO;

class UserCodeTypeCreateUseCaseHandler
{
    private UserCodeTypeCreateUseCase $useCase;

    public function __construct(UserCodeTypeCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(UserCodeTypeRequestDTO $request): UserCodeTypeResponseDTO
    {
        return $this->useCase->execute($request);
    }
}

