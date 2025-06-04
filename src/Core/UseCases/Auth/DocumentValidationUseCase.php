<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\DocumentValidationRequestDTO;

interface DocumentValidationUseCase
{
    public function execute(DocumentValidationRequestDTO $dto): ?array;
}