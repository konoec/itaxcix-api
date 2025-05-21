<?php

namespace itaxcix\Shared\Validators\rules\document;

class RucRule {
    public function validate(string $value): array {
        if (!preg_match('/^\d{11}$/', $value)) {
            return ['El RUC debe tener exactamente 11 dígitos.'];
        }

        return [];
    }
}