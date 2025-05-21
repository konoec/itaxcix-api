<?php

namespace itaxcix\Shared\DTO\useCases;

readonly class ContactTypeResponseDTO {
    public function __construct(
        public int $id,
        public string $name
    ) {}
}