<?php

namespace itaxcix\Shared\DTO\generic;

use DateTimeImmutable;

readonly class BaseResponseDTO {
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public mixed $data = null,
        public array|null $error = null,
        public DateTimeImmutable $timestamp = new DateTimeImmutable()
    ) {}
}