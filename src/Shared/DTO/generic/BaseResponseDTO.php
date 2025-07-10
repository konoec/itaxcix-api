<?php

namespace itaxcix\Shared\DTO\generic;

use DateTimeImmutable;
use JsonSerializable;

readonly class BaseResponseDTO implements JsonSerializable {
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public mixed $data = null,
        public array|null $error = null,
        public DateTimeImmutable $timestamp = new DateTimeImmutable()
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'error' => $this->error,
            'timestamp' => $this->timestamp
        ];
    }
}