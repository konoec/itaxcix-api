<?php

namespace itaxcix\Core\Handler\UserCodeType;

use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeListUseCase;
use itaxcix\Shared\DTO\useCases\UserCodeType\UserCodeTypePaginationRequestDTO;

class UserCodeTypeListUseCaseHandler
{
    private UserCodeTypeListUseCase $useCase;

    public function __construct(UserCodeTypeListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(UserCodeTypePaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

