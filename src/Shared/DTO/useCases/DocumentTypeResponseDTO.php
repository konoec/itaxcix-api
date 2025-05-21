<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class DocumentTypeResponseDTO {
    public function __construct(
        public int $id,
        public string $name
    ) {}
}