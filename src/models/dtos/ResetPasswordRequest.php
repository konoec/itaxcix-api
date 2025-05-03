<?php

namespace itaxcix\model\dtos;

class ResetPasswordRequest {
    public function __construct(
        public readonly int $userId,
        public readonly string $newPassword
    ) {}
}
