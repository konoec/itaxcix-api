<?php

namespace itaxcix\Shared\Validators\useCases\Auth;

use itaxcix\Shared\Validators\generic\IdValidator;

class ResendVerificationCodeValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['userId'])) {
            $errors['userId'] = 'El ID del usuario es requerido.';
        } elseif (!is_int($data['userId']) && !ctype_digit((string) $data['userId'])) {
            $errors['userId'] = 'El ID del usuario debe ser un número entero válido.';
        } else {
            $idErrors = IdValidator::validate((int) $data['userId']);
            if (!empty($idErrors)) {
                $errors['userId'] = reset($idErrors);
            }
        }

        if (!empty($errors)) {
            return count($errors) > 1
                ? ['Los datos proporcionados no son válidos.']
                : [reset($errors)];
        }

        return [];
    }
}