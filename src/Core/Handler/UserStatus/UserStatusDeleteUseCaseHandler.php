<?php

namespace itaxcix\Core\Handler\UserStatus;

use itaxcix\Core\UseCases\UserStatus\UserStatusDeleteUseCase;

class UserStatusDeleteUseCaseHandler
{
    private UserStatusDeleteUseCase $useCase;

    public function __construct(UserStatusDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
