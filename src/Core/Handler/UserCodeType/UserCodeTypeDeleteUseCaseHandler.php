<?php

namespace itaxcix\Core\Handler\UserCodeType;

use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeDeleteUseCase;

class UserCodeTypeDeleteUseCaseHandler
{
    private UserCodeTypeDeleteUseCase $useCase;

    public function __construct(UserCodeTypeDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}

