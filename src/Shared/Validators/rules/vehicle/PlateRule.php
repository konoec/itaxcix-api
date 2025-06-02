<?php

namespace itaxcix\Shared\Validators\rules\vehicle;

class PlateRule {
    public function validate(string $value): array {
        // Aceptar solo si tiene 6 o 7 caracteres alfanuméricos (mayúsculas y números)
        if (preg_match('/^[A-Z0-9]{6,7}$/', $value)) {
            return [];
        }

        return ['La placa no tiene un formato válido.'];
    }
}