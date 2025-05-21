<?php

namespace itaxcix\Shared\Validators\generic;

class IdValidator {
    public static function validate(int $value): array {
        if ($value <= 0) {
            return ['El ID debe ser mayor que cero.'];
        }

        return [];
    }
}