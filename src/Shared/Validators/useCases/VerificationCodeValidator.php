<?php

namespace itaxcix\Shared\Validators\useCases;

use itaxcix\Shared\Validators\generic\IdValidator;
use itaxcix\Shared\Validators\rules\auth\VerificationCodeRule;

class VerificationCodeValidator {
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

        // Validar code
        if (!isset($data['code'])) {
            $errors['code'] = 'El código es requerido.';
        } else if (!is_string($data['code'])) {
            $errors['code'] = 'El código debe ser una cadena válida.';
        } else {
            $codeRule = new VerificationCodeRule();
            $codeErrors = $codeRule->validate($data['code']);

            if (!empty($codeErrors)) {
                $errors['code'] = reset($codeErrors);
            }
        }

        // Devolver errores específicos o mensaje general
        if (!empty($errors)) {
            if (count($errors) > 1) {
                return ['Los datos proporcionados no son válidos.'];
            }

            return [reset($errors)];
        }

        return [];
    }
}