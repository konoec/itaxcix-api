<?php

namespace itaxcix\Core\UseCases\Profile;

use itaxcix\Shared\DTO\useCases\Profile\ChangeEmailRequestDTO;

interface ChangeEmailUseCase
{
    public function execute(ChangeEmailRequestDTO $dto): ?array;
}
