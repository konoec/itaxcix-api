<?php

namespace itaxcix\Shared\Validators\rules\contact;

class MobilePhoneRule {
    public function validate(string $value): array {
        // Formato esperado: +51987654321
        if (!preg_match('/^\+\d{10,15}$/', $value)) {
            return ['El número de teléfono debe estar en formato internacional (ej: +51987654321).'];
        }

        return [];
    }
}