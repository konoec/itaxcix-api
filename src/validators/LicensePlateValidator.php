<?php

namespace itaxcix\validators;

use Exception;

class LicensePlateValidator {

    /**
     * Válida la placa del vehículo según el formato peruano.
     *
     * @param string $licensePlate La placa a validar.
     * @throws Exception Si la placa no cumple con el formato esperado.
     */
    public static function validate(string $licensePlate): void {
        if (!preg_match('/^[A-Z0-9]{6}$/i', $licensePlate)) {
            throw new Exception("La placa del vehículo es inválida. Formato esperado: 6 caracteres alfanuméricos (ej. M2T511).", 400);
        }
    }
}