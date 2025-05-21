<?php

namespace itaxcix\Shared\Validators\rules\vehicle;

class PlateRule {
    public function validate(string $value): array {
        // Placas nuevas: AAA-123 ó AAA123
        if (preg_match('/^[A-Z]{3}-?\d{3}$/', $value)) {
            return [];
        }

        // Placas antiguas: AA123AA
        if (preg_match('/^[A-Z]{2}\d{3}[A-Z]{2}$/', $value)) {
            return [];
        }

        return ['La placa no tiene un formato válido (ej: ABC-123 o AB123CD).'];
    }
}