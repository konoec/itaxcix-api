<?php

namespace itaxcix\validators;

use Exception;

class PasswordValidator {

    /**
     * Válida la contraseña según las reglas definidas.
     *
     * @param string $password La contraseña a validar.
     * @throws Exception Si la contraseña no cumple con las reglas.
     */
    public static function validate(string $password): void {
        if (strlen($password) < 8) {
            throw new Exception("La contraseña debe tener al menos 8 caracteres.", 400);
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("La contraseña debe contener al menos una letra mayúscula.", 400);
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("La contraseña debe contener al menos una letra minúscula.", 400);
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("La contraseña debe contener al menos un número.", 400);
        }

        if (!preg_match('/[\W_]/', $password)) {
            throw new Exception("La contraseña debe contener al menos un carácter especial (ej: !, @, #).", 400);
        }
    }
}
