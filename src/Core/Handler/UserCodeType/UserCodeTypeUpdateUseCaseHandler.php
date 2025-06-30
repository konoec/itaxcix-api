<?php

namespace itaxcix\Core\Handler\UserCodeType;

use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeUpdateUseCase;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypeResponseDTO;

class UserCodeTypeUpdateUseCaseHandler
{
    private UserCodeTypeUpdateUseCase $useCase;

    public function __construct(UserCodeTypeUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, UserCodeTypeRequestDTO $request): UserCodeTypeResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}

