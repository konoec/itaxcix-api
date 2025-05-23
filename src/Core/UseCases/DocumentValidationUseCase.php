<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\DocumentValidationRequestDTO;

interface DocumentValidationUseCase
{
    public function execute(DocumentValidationRequestDTO $dto): ?array;
}