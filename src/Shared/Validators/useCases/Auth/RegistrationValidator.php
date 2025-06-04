<?php

namespace itaxcix\Shared\Validators\useCases\Auth;

use itaxcix\Shared\Validators\generic\IdValidator;
use itaxcix\Shared\Validators\generic\PasswordValidator;
use itaxcix\Shared\Validators\rules\contact\EmailRule;
use itaxcix\Shared\Validators\rules\contact\MobilePhoneRule;

class RegistrationValidator {
    private const CONTACT_RULE_MAP = [
        1 => EmailRule::class,
        2 => MobilePhoneRule::class,
    ];

    public function validate(array $data): array {
        $errors = [];

        // Validar password
        if (!isset($data['password'])) {
            $errors['password'] = 'La contraseña es requerida.';
        } else if (!is_string($data['password'])) {
            $errors['password'] = 'La contraseña debe ser una cadena válida.';
        } else {
            $passwordErrors = PasswordValidator::validate($data['password']);
            if (!empty($passwordErrors)) {
                $errors['password'] = reset($passwordErrors);
            }
        }

        // Validar contactTypeId
        if (!isset($data['contactTypeId'])) {
            $errors['contactTypeId'] = 'El tipo de contacto es requerido.';
        } else if (!is_int($data['contactTypeId']) && !ctype_digit($data['contactTypeId'])) {
            $errors['contactTypeId'] = 'El tipo de contacto debe ser un número entero válido.';
        } else {
            $contactTypeId = $data['contactTypeId'];

            if (!array_key_exists($contactTypeId, self::CONTACT_RULE_MAP)) {
                $errors['contactTypeId'] = 'El tipo de contacto no es válido.';
            } else {
                // Validar contactValue según el tipo
                if (!isset($data['contactValue'])) {
                    $errors['contactValue'] = 'El valor de contacto es requerido.';
                } else if (!is_string($data['contactValue'])) {
                    $errors['contactValue'] = 'El valor de contacto debe ser una cadena válida.';
                } else {
                    $rule = new (self::CONTACT_RULE_MAP[$contactTypeId])();
                    $contactErrors = $rule->validate($data['contactValue']);

                    if (!empty($contactErrors)) {
                        $errors['contactValue'] = reset($contactErrors);
                    }
                }
            }
        }

        // Validar personId
        if (!isset($data['personId'])) {
            $errors['personId'] = 'El ID de persona es requerido.';
        } else if (!is_int($data['personId']) && !ctype_digit($data['personId'])) {
            $errors['personId'] = 'El ID de persona debe ser un número entero válido.';
        } else {
            $personIdErrors = IdValidator::validate((int)$data['personId']);
            if (!empty($personIdErrors)) {
                $errors['personId'] = reset($personIdErrors);
            }
        }

        // Validar vehicleId (opcional)
        if (isset($data['vehicleId'])) {
            if (!is_int($data['vehicleId']) && !ctype_digit($data['vehicleId'])) {
                $errors['vehicleId'] = 'El ID del vehículo debe ser un número entero válido.';
            } else {
                $vehicleIdErrors = IdValidator::validate((int)$data['vehicleId']);
                if (!empty($vehicleIdErrors)) {
                    $errors['vehicleId'] = reset($vehicleIdErrors);
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