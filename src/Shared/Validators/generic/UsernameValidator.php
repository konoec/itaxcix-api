<?php

namespace itaxcix\Shared\Validators\generic;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class UsernameValidator {
    public static function validate(string $value): array {
        $usernameValidator = v::notEmpty()
            ->length(3, 20)
            ->alnum()
            ->setName('Nombre de usuario');

        try {
            $usernameValidator->assert($value);
            return [];
        } catch (ValidationException $e) {
            return ['El nombre de usuario no es válido.'];
        }
    }
}