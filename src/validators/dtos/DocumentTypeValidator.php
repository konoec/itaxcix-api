<?php

namespace itaxcix\validators\dtos;

use Exception;

class DocumentTypeValidator {

    /**
     * Válida el tipo de documento según las reglas definidas.
     *
     * @param int $documentTypeId El tipo de documento (1: DNI, 2: Pasaporte, 3: Carné, 4: RUC).
     * @throws Exception Si el tipo de documento no es válido.
     */
    public static function validate(int $documentTypeId): void {
        if (!in_array($documentTypeId, [1, 2, 3, 4])) {
            throw new Exception("Tipo de documento inválido.", 400);
        }
    }
}
