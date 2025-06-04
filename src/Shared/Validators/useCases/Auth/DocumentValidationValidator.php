<?php

namespace itaxcix\Shared\Validators\useCases\Auth;

use itaxcix\Shared\Validators\rules\document\DniRule;
use itaxcix\Shared\Validators\rules\document\ForeignerIdRule;
use itaxcix\Shared\Validators\rules\document\PassportRule;
use itaxcix\Shared\Validators\rules\document\RucRule;

class DocumentValidationValidator {
    private const RULE_MAP = [
        1 => DniRule::class,
        2 => RucRule::class,
        3 => PassportRule::class,
        4 => ForeignerIdRule::class,
    ];

    public function validate(array $data): array {
        $errors = [];

        // Validar documentTypeId
        if (!isset($data['documentTypeId'])) {
            $errors['documentTypeId'] = 'El tipo de documento es requerido.';
        } else if (!array_key_exists($data['documentTypeId'], self::RULE_MAP)) {
            $errors['documentTypeId'] = 'El tipo de documento no es válido.';
        }

        // Validar documentValue
        if (!isset($data['documentValue'])) {
            $errors['documentValue'] = 'El valor del documento es requerido.';
        } else if (!is_string($data['documentValue'])) {
            $errors['documentValue'] = 'El valor del documento debe ser una cadena.';
        } else {
            $typeId = $data['documentTypeId'] ?? null;

            if ($typeId && array_key_exists($typeId, self::RULE_MAP)) {
                $rule = new (self::RULE_MAP[$typeId])();
                $documentErrors = $rule->validate($data['documentValue']);

                if (!empty($documentErrors)) {
                    $errors['documentValue'] = reset($documentErrors);
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