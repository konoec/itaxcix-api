<?php

namespace itaxcix\Shared\Validators\useCases\Auth;

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
            $errors['newPassword'] = 'La nueva contraseña debe ser una cadena de texto.';
        } else {
            $passwordErrors = PasswordValidator::validate($data['newPassword']);
            if (!empty($passwordErrors)) {
                $errors['newPassword'] = reset($passwordErrors);
            }
        }

        // Validar repeatPassword
        if (!isset($data['repeatPassword'])) {
            $errors['repeatPassword'] = 'Repetir la contraseña es obligatorio.';
        } else if (!is_string($data['repeatPassword'])) {
            $errors['repeatPassword'] = 'La repetición de la contraseña debe ser una cadena.';
        } else if (empty($data['newPassword'])) {
            // Evita hacer la comparación si newPassword no está definido
            // Esto se resolverá en otro paso
        } else if ($data['newPassword'] !== $data['repeatPassword']) {
            $errors['repeatPassword'] = 'Las contraseñas no coinciden.';
        }

        // Si hay errores, devolvemos un mensaje general o específico
        if (!empty($errors)) {
            return count($errors) > 1
                ? ['Los datos proporcionados no son válidos.']
                : [reset($errors)];
        }

        return [];
    }
}