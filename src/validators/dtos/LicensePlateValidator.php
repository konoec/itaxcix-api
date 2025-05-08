<?php

namespace itaxcix\validators\dtos;

use Exception;

class LicensePlateValidator {

    /**
     * Válida la placa del vehículo según las reglas definidas.
     *
     * @param string $licensePlate La placa a validar.
     * @throws Exception Si la placa no cumple con las reglas.
     */
    public static function validate(string $licensePlate): void {
        if (!preg_match('/^[A-Z0-9]{2,3}-\d{3,4}$/', $licensePlate)) {
            throw new Exception("La placa del vehículo es inválida. Formato esperado: ABC-123 o AB-1234.", 400);
        }
    }
}
