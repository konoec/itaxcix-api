<?php

namespace itaxcix\model\dtos;

class VerifyCodeRequest {
    public function __construct(
        public readonly string $code,
        public readonly ?string $email = null,
        public readonly ?string $phone = null
    ) {
        if (!$email && !$phone) {
            throw new \InvalidArgumentException("Se requiere email o teléfono");
        }
    }
}