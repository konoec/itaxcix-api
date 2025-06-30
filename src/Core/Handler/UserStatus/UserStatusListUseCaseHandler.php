<?php

namespace itaxcix\Core\Handler\UserStatus;

use itaxcix\Core\UseCases\UserStatus\UserStatusListUseCase;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusPaginationRequestDTO;

class UserStatusListUseCaseHandler
{
    private UserStatusListUseCase $useCase;

    public function __construct(UserStatusListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(UserStatusPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}
