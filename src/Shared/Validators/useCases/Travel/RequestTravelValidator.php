<?php

namespace itaxcix\Shared\Validators\useCases\Travel;

use itaxcix\Shared\Validators\generic\IdValidator;
use itaxcix\Shared\Validators\generic\CoordinatesValidator;

class RequestTravelValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validación de IDs
        if (!isset($data['citizenId'])) {
            $errors['citizenId'] = 'El ID del ciudadano es requerido.';
        } elseif ($idErrors = IdValidator::validate((int)$data['citizenId'])) {
            $errors['citizenId'] = reset($idErrors);
        }

        if (!isset($data['driverId'])) {
            $errors['driverId'] = 'El ID del conductor es requerido.';
        } elseif ($idErrors = IdValidator::validate((int)$data['driverId'])) {
            $errors['driverId'] = reset($idErrors);
        }

        // Validación de coordenadas
        foreach (['origin', 'destination'] as $point) {
            $latKey = "{$point}Latitude";
            $lonKey = "{$point}Longitude";
            $districtKey = "{$point}District";
            $addressKey = "{$point}Address";

            if (!isset($data[$latKey]) || !is_numeric($data[$latKey])) {
                $errors[$latKey] = "La latitud de $point es requerida y debe ser numérica.";
            } elseif ($latErrors = CoordinatesValidator::validateLatitude((float)$data[$latKey])) {
                $errors[$latKey] = reset($latErrors);
            }

            if (!isset($data[$lonKey]) || !is_numeric($data[$lonKey])) {
                $errors[$lonKey] = "La longitud de $point es requerida y debe ser numérica.";
            } elseif ($lonErrors = CoordinatesValidator::validateLongitude((float)$data[$lonKey])) {
                $errors[$lonKey] = reset($lonErrors);
            }

            if (empty($data[$districtKey])) {
                $errors[$districtKey] = "El distrito de $point es requerido.";
            } elseif (strlen($data[$districtKey]) > 100) {
                $errors[$districtKey] = "El distrito de $point no puede exceder los 100 caracteres.";
            }

            if (empty($data[$addressKey])) {
                $errors[$addressKey] = "La dirección de $point es requerida.";
            } elseif (strlen($data[$addressKey]) > 255) {
                $errors[$addressKey] = "La dirección de $point no puede exceder los 255 caracteres.";
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