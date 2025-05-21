<?php

namespace itaxcix\Shared\Validators\generic;

class Base64ImageValidator {
    public static function validate(string $value): array {
        // Verificar formato Base64 básico
        if (!preg_match('/^data:image\/(png|jpeg|jpg|gif);base64,([a-zA-Z0-9+\/=]+)/', $value)) {
            return ['La imagen debe estar codificada en Base64 y tener un formato válido (png, jpg, gif).'];
        }

        return [];
    }
}