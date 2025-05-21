<?php

namespace itaxcix\Shared\Validators\rules\auth;

class VerificationCodeRule {
    public function validate(string $value): array {
        if (!preg_match('/^[A-Z0-9]{6}$/', $value)) {
            return ['El código debe tener exactamente 6 caracteres alfanuméricos (A-Z, 0-9).'];
        }

        return [];
    }
}