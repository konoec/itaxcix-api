<?php

namespace itaxcix\validators;

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
                if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("El correo electrónico es inválido.", 400);
                }
                break;

            case 2:
                if (!preg_match('/^\+\d{10,15}$/', $contact)) {
                    throw new Exception("El número debe estar en formato internacional (ej: +51987654321).");
                }
                break;

            default:
                throw new Exception("Tipo de contacto inválido.", 400);
        }
    }
}