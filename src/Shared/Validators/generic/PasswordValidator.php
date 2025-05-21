<?php

namespace itaxcix\Shared\Validators\generic;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class PasswordValidator {
    public static function validate(string $value): array {
        $passwordValidator = v::notEmpty()
            ->length(min: 8)
            ->regex('/[A-Z]/')
            ->regex('/[a-z]/')
            ->regex('/[0-9]/')
            ->regex('/[!@#$%^&*(),.?":{}|<>]/')
            ->setName('Contraseña');

        try {
            $passwordValidator->assert($value);
            return [];
        } catch (ValidationException $e) {
            return ['La contraseña no cumple con los requisitos mínimos.'];
        }
    }
}