<?php

namespace itaxcix\Shared\Validators\useCases\Profile;

class VerifyPhoneChangeRequestValidator
{
    public function validate(array $data): array
    {
        // Validar código
        if (!isset($data['code'])) {
            return ['El código de verificación es requerido.'];
        }

        if (!is_string($data['code'])) {
            return ['El código debe ser texto.'];
        }

        if (strlen($data['code']) !== 6) {
            return ['El código debe tener 6 dígitos.'];
        }

        if (!ctype_digit($data['code'])) {
            return ['El código debe contener solo números.'];
        }

        return [];
    }
}
