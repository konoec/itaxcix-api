<?php

namespace itaxcix\Shared\Validators\rules\document;

class PassportRule {
    public function validate(string $value): array{
        if (!preg_match('/^[A-Z]{1,2}\d{6,8}$/i', $value)) {
            return ['El pasaporte debe tener formato válido (ej: PE1234567).'];
        }

        return [];
    }
}