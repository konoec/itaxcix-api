<?php

namespace itaxcix\Shared\Validators\useCases\Admission;

use itaxcix\Shared\Validators\generic\IdValidator;

class RejectDriverValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['driverId'])) {
            $errors['driverId'] = 'El ID del conductor es requerido.';
        } elseif (!is_int($data['driverId']) && !ctype_digit((string) $data['driverId'])) {
            $errors['driverId'] = 'El ID del conductor debe ser un número entero válido.';
        } else {
            $idErrors = IdValidator::validate((int) $data['driverId']);
            if (!empty($idErrors)) {
                $errors['driverId'] = reset($idErrors);
            }
        }

        if (!empty($errors)) {
            return count($errors) > 1
                ? ['Los datos proporcionados no son válidos.']
                : [reset($errors)];
        }

        return [];
    }
}