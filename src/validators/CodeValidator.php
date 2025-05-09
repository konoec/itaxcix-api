<?php

namespace itaxcix\validators;

use Exception;

class CodeValidator {

    /**
     * Válida el código según las reglas definidas.
     *
     * @param string $code El código a validar.
     * @throws Exception Si el código no cumple con las reglas.
     */
    public static function validate(string $code): void {
        if (strlen($code) !== 6) {
            throw new Exception("El código debe tener exactamente 6 caracteres.", 400);
        }

        if (!preg_match('/^[A-Za-z0-9]{6}$/', $code)) {
            throw new Exception("El código debe ser alfanumérico y tener 6 caracteres.", 400);
        }
    }
}