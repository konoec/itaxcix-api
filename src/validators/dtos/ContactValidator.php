<?php

namespace itaxcix\validators\dtos;

use Exception;

class ContactValidator {

    /**
     * Válida el contacto según las reglas definidas.
     *
     * @param string $contact El contacto a validar.
     * @param int $typeId El tipo de contacto (1: Email, 2: Teléfono).
     * @throws Exception Si el contacto no cumple con las reglas.
     */
    public static function validate(string $contact, int $typeId): void {
        switch ($typeId) {
            case 1:
                if (!preg_match(FILTER_VALIDATE_EMAIL, $contact)) {
                    throw new Exception("El correo electrónico es inválido.", 400);
                }
                break;
            case 2:
                if (!preg_match('/^9\d{8}$/', $contact)) {
                    throw new Exception("El número de teléfono móvil debe comenzar con 9 y tener 9 dígitos.", 400);
                }
                break;
            default:
                throw new Exception("Tipo de contacto inválido.", 400);
        }
    }
}