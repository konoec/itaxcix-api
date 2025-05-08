<?php

namespace itaxcix\validators\dtos;

use Exception;

class DocumentValidator {

    /**
     * Válida el documento según el tipo especificado.
     *
     * @param string $document El documento a validar.
     * @param int $typeId El tipo de documento (1: DNI, 2: Pasaporte, 3: Carné, 4: RUC).
     * @throws Exception Si el documento no cumple con las reglas.
     */
    public static function validate(string $document, int $typeId): void {
        switch ($typeId) {
            case 1:
                if (!preg_match('/^\d{8}$/', $document)) {
                    throw new Exception("El DNI debe tener 8 dígitos.", 400);
                }
                break;
            case 2:
            case 3:
                if (!preg_match('/^[a-zA-Z0-9]{8,12}$/', $document)) {
                    throw new Exception("El documento debe tener entre 8 y 12 caracteres alfanuméricos.", 400);
                }
                break;
            case 4:
                if (!preg_match('/^\d{11}$/', $document)) {
                    throw new Exception("El RUC debe tener 11 dígitos.", 400);
                }
                break;
            default:
                throw new Exception("Tipo de documento inválido.", 400);
        }
    }
}
