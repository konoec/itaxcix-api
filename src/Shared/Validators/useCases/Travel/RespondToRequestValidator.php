<?php

namespace itaxcix\Shared\Validators\useCases\Travel;

use itaxcix\Shared\Validators\generic\IdValidator;

class RespondToRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['travelId'])) {
            $errors['travelId'] = 'El ID del viaje es requerido.';
        } elseif ($idErrors = IdValidator::validate((int)$data['travelId'])) {
            $errors['travelId'] = reset($idErrors);
        }

        if (!isset($data['accepted'])) {
            $errors['accepted'] = 'La respuesta de aceptación es requerida.';
        } elseif (!is_bool($data['accepted'])) {
            $errors['accepted'] = 'La respuesta debe ser verdadero o falso.';
        }

        if (!empty($errors)) {
            return count($errors) > 1
                ? ['Los datos proporcionados no son válidos.']
                : [reset($errors)];
        }

        return [];
    }
}