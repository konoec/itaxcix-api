<?php

namespace itaxcix\Shared\Validators\rules\contact;

class EmailRule {
    public function validate(string $value): array {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ['El correo electrónico no tiene un formato válido.'];
        }

        return [];
    }
}