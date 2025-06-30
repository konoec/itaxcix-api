<?php

namespace itaxcix\Core\Handler\UserStatus;

use itaxcix\Core\UseCases\UserStatus\UserStatusUpdateUseCase;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusRequestDTO;

class UserStatusUpdateUseCaseHandler
{
    private UserStatusUpdateUseCase $useCase;

    public function __construct(UserStatusUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, UserStatusRequestDTO $request): array
    {
        return $this->useCase->execute($id, $request);
    }
}
