<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class DocumentValidationRequestDTO{
    public function __construct(
        public int $documentTypeId,
        public string $documentValue,
    ) {}
}