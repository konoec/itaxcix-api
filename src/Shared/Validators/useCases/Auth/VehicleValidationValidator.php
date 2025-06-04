<?php

namespace itaxcix\Shared\Validators\useCases\Auth;

use itaxcix\Shared\Validators\rules\document\DniRule;
use itaxcix\Shared\Validators\rules\document\ForeignerIdRule;
use itaxcix\Shared\Validators\rules\document\PassportRule;
use itaxcix\Shared\Validators\rules\document\RucRule;
use itaxcix\Shared\Validators\rules\vehicle\PlateRule;

class VehicleValidationValidator {
    private const DOCUMENT_RULE_MAP = [
        1 => DniRule::class,
        2 => PassportRule::class,
        3 => ForeignerIdRule::class,
        4 => RucRule::class,
    ];

    public function validate(array $data): array {
        $errors = [];

        // Validar documentTypeId
        if (!isset($data['documentTypeId'])) {
            $errors['documentTypeId'] = 'El tipo de documento es requerido.';
        } else if (!is_int($data['documentTypeId']) && !ctype_digit($data['documentTypeId'])) {
            $errors['documentTypeId'] = 'El tipo de documento debe ser un número entero válido.';
        } else {
            $documentTypeId = $data['documentTypeId'];

            if (!array_key_exists($documentTypeId, self::DOCUMENT_RULE_MAP)) {
                $errors['documentTypeId'] = 'El tipo de documento no es válido.';
            }
        }

        // Validar documentValue
        if (!isset($data['documentValue'])) {
            $errors['documentValue'] = 'El valor del documento es requerido.';
        } else if (!is_string($data['documentValue'])) {
            $errors['documentValue'] = 'El valor del documento debe ser una cadena válida.';
        } else if (isset($documentTypeId)) {
            $rule = new (self::DOCUMENT_RULE_MAP[$documentTypeId])();
            $documentErrors = $rule->validate($data['documentValue']);

            if (!empty($documentErrors)) {
                $errors['documentValue'] = reset($documentErrors);
            }
        }

        // Validar plateValue
        if (!isset($data['plateValue'])) {
            $errors['plateValue'] = 'La placa es requerida.';
        } else if (!is_string($data['plateValue'])) {
            $errors['plateValue'] = 'La placa debe ser una cadena válida.';
        } else {
            $plateRule = new PlateRule();
            $plateErrors = $plateRule->validate($data['plateValue']);

            if (!empty($plateErrors)) {
                $errors['plateValue'] = reset($plateErrors);
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