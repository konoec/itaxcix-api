<?php

namespace itaxcix\Shared\Validators\useCases\Auth;

use itaxcix\Shared\Validators\generic\PasswordValidator;
use itaxcix\Shared\Validators\rules\document\DniRule;
use itaxcix\Shared\Validators\rules\document\ForeignerIdRule;
use itaxcix\Shared\Validators\rules\document\PassportRule;
use itaxcix\Shared\Validators\rules\document\RucRule;

class AuthLoginValidator {
    private const DOCUMENT_RULES = [
        DniRule::class,
        RucRule::class,
        PassportRule::class,
        ForeignerIdRule::class,
    ];

    public function validate(array $data): array {
        $errors = [];

        // Validar documentValue
        if (!isset($data['documentValue'])) {
            $errors['documentValue'] = 'El número de documento es requerido.';
        } else if (!is_string($data['documentValue'])) {
            $errors['documentValue'] = 'El documento debe ser una cadena válida.';
        } else {
            $documentValid = false;

            foreach (self::DOCUMENT_RULES as $rule) {
                $documentErrors = (new $rule())->validate($data['documentValue']);
                if (empty($documentErrors)) {
                    $documentValid = true;
                    break;
                }
            }

            if (!$documentValid) {
                $errors['documentValue'] = 'El documento no tiene un formato válido.';
            }
        }

        // Validar password
        if (!isset($data['password'])) {
            $errors['password'] = 'La contraseña es requerida.';
        } else if (!is_string($data['password'])) {
            $errors['password'] = 'La contraseña debe ser una cadena.';
        } else {
            $passwordErrors = PasswordValidator::validate($data['password']);
            if (!empty($passwordErrors)) {
                $errors['password'] = reset($passwordErrors);
            }
        }

        // Devolver errores
        if (!empty($errors)) {
            return count($errors) > 1
                ? ['Los datos proporcionados no son válidos.']
                : [reset($errors)];
        }

        return [];
    }
}