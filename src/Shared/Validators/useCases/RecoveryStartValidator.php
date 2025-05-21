<?php

namespace itaxcix\Shared\Validators\useCases;

use itaxcix\Shared\Validators\rules\contact\EmailRule;
use itaxcix\Shared\Validators\rules\contact\MobilePhoneRule;

class RecoveryStartValidator {
    private const RULE_MAP = [
        1 => EmailRule::class,
        2 => MobilePhoneRule::class,
    ];

    public function validate(array $data): array {
        $errors = [];

        // Validar contactTypeId
        if (!isset($data['contactTypeId'])) {
            $errors['contactTypeId'] = 'El tipo de contacto es requerido.';
        } else if (!is_int($data['contactTypeId']) && !ctype_digit($data['contactTypeId'])) {
            $errors['contactTypeId'] = 'El tipo de contacto debe ser un número entero válido.';
        } else {
            $typeId = (int)$data['contactTypeId'];

            if (!array_key_exists($typeId, self::RULE_MAP)) {
                $errors['contactTypeId'] = 'El tipo de contacto no es válido.';
            }
        }

        // Validar contactValue
        if (!isset($data['contactValue'])) {
            $errors['contactValue'] = 'El valor de contacto es requerido.';
        } else if (!is_string($data['contactValue'])) {
            $errors['contactValue'] = 'El valor de contacto debe ser una cadena válida.';
        } else {
            $typeId = $data['contactTypeId'] ?? null;

            if ($typeId && array_key_exists($typeId, self::RULE_MAP)) {
                $rule = new (self::RULE_MAP[$typeId])();
                $contactErrors = $rule->validate($data['contactValue']);

                if (!empty($contactErrors)) {
                    $errors['contactValue'] = reset($contactErrors);
                }
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