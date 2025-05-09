<?php

namespace itaxcix\validators;

use Exception;

class ContactTypeValidator {

    /**
     * Válida el tipo de contacto según las reglas definidas.
     *
     * @param int $contactTypeId El tipo de contacto (1: Correo, 2: Teléfono).
     * @throws Exception Si el tipo de contacto no es válido.
     */
    public static function validate(int $contactTypeId): void {
        if (!in_array($contactTypeId, [1, 2])) {
            throw new Exception("Tipo de contacto inválido.", 400);
        }
    }
}
