<?php

namespace itaxcix\Shared\Validators\rules\document;

class DniRule {
    public function validate(string $value): array {
        if (!preg_match('/^\d{8}$/', $value)) {
            return ['El DNI debe tener exactamente 8 dígitos.'];
        }

        return [];
    }
}

