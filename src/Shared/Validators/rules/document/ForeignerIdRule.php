<?php

namespace itaxcix\Shared\Validators\rules\document;

class ForeignerIdRule
{
    public function validate(string $value): array {
        if (!preg_match('/^[EXex]\d{8}$/', $value)) {
            return ['El carné de extranjería debe comenzar con E/X seguido de 8 dígitos (ej: E12345678).'];
        }

        return [];
    }
}