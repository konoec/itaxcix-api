<?php

namespace itaxcix\validators;

use Exception;

class ContactValidator {

    /**
     * Válida el contacto. Si se proporciona $typeId, valida estrictamente por tipo.
     * Si no, intenta validar como email o teléfono automáticamente.
     *
     * @param string $contact El contacto a validar.
     * @param int|null $typeId Tipo de contacto (1: Email, 2: Teléfono), opcional.
     * @throws Exception Si el contacto no cumple con las reglas.
     */
    public static function validate(string $contact, ?int $typeId = null): void {
        if ($typeId !== null) {
            // Validación específica por tipo
            switch ($typeId) {
                case 1:
                    if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception("El correo electrónico es inválido.", 400);
                    }
                    break;

                case 2:
                    if (!preg_match('/^\+\d{10,15}$/', $contact)) {
                        throw new Exception("El número debe estar en formato internacional (ej: +51987654321).", 400);
                    }
                    break;

                default:
                    throw new Exception("Tipo de contacto inválido.", 400);
            }
        } else {
            // Validación automática: intenta como email o teléfono
            if (!filter_var($contact, FILTER_VALIDATE_EMAIL) && !preg_match('/^\+\d{10,15}$/', $contact)) {
                throw new Exception("El formato del contacto es inválido.", 400);
            }
        }
    }
}