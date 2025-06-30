<?php

namespace itaxcix\Core\Handler\UserStatus;

use itaxcix\Core\UseCases\UserStatus\UserStatusCreateUseCase;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusRequestDTO;

class UserStatusCreateUseCaseHandler
{
    private UserStatusCreateUseCase $useCase;

    public function __construct(UserStatusCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(UserStatusRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}
