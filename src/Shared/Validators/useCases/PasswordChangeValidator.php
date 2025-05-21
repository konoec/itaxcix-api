<?php

namespace itaxcix\Shared\Validators\useCases;

use itaxcix\Shared\Validators\generic\IdValidator;
use itaxcix\Shared\Validators\generic\PasswordValidator;

class PasswordChangeValidator {
    public function validate(array $data): array {
        $errors = [];

        // Validar userId
        if (!isset($data['userId'])) {
            $errors['userId'] = 'El ID de usuario es requerido.';
        } else if (!is_int($data['userId']) && !ctype_digit($data['userId'])) {
            $errors['userId'] = 'El ID de usuario debe ser un número entero válido.';
        } else {
            $idErrors = IdValidator::validate((int)$data['userId']);
            if (!empty($idErrors)) {
                $errors['userId'] = reset($idErrors);
            }
        }

        // Validar newPassword
        if (!isset($data['newPassword'])) {
            $errors['newPassword'] = 'La nueva contraseña es requerida.';
        } else if (!is_string($data['newPassword'])) {
            $errors['newPassword'] = 'La contraseña debe ser una cadena de texto.';
        } else {
            $passwordErrors = PasswordValidator::validate($data['newPassword']);
            if (!empty($passwordErrors)) {
                $errors['newPassword'] = reset($passwordErrors);
            }
        }

        // Devolver mensaje general si hay más de un error
        if (count($errors) > 1) {
            return ['Los datos proporcionados no son válidos.'];
        }

        return $errors;
    }
}