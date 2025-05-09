<?php

namespace itaxcix\validators;

use Exception;

class AliasValidator {

    /**
     * Válida el alias según las reglas definidas.
     *
     * @param string $alias El alias a validar.
     * @throws Exception Si el alias no cumple con las reglas.
     */
    public static function validate(string $alias): void {
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $alias)) {
            throw new Exception("Alias inválido. Debe tener entre 3 y 20 caracteres alfanuméricos o guiones bajos.", 400);
        }
    }
}