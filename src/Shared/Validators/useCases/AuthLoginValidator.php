<?php

namespace itaxcix\Shared\Validators\useCases;

use itaxcix\Shared\Validators\generic\PasswordValidator;
use itaxcix\Shared\Validators\generic\UsernameValidator;

class AuthLoginValidator {
    public function validate(array $data): array {
        $errors = [];

        // Validar username
        if (!isset($data['username'])) {
            $errors['username'] = 'El nombre de usuario es requerido.';
        } else {
            $usernameErrors = UsernameValidator::validate($data['username']);
            if (!empty($usernameErrors)) {
                $errors['username'] = reset($usernameErrors);
            }
        }

        // Validar password
        if (!isset($data['password'])) {
            $errors['password'] = 'La contraseña es requerida.';
        } else {
            $passwordErrors = PasswordValidator::validate($data['password']);
            if (!empty($passwordErrors)) {
                $errors['password'] = reset($passwordErrors);
            }
        }

        // Si hay errores, devolvemos un array con los mensajes específicos
        if (!empty($errors)) {
            // Si hay más de un error, devolvemos el mensaje global
            if (count($errors) > 1) {
                return ['Los datos proporcionados no son válidos.'];
            }

            // Si solo hay un error, devolvemos ese mensaje específico
            return [reset($errors)];
        }

        return [];
    }
}