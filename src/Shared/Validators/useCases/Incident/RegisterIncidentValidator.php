<?php

namespace itaxcix\Shared\Validators\useCases\Incident;

class RegisterIncidentValidator {
    public function validate(array $data): array {
        $errors = [];
        if (!isset($data['userId']) || !is_numeric($data['userId'])) {
            $errors['userId'] = 'El ID de usuario es requerido y debe ser numérico.';
        }
        if (!isset($data['travelId']) || !is_numeric($data['travelId'])) {
            $errors['travelId'] = 'El ID de viaje es requerido y debe ser numérico.';
        }
        if (empty($data['typeName']) || !is_string($data['typeName'])) {
            $errors['typeName'] = 'El nombre del tipo de incidencia es requerido.';
        }
        if (isset($data['comment']) && !is_string($data['comment'])) {
            $errors['comment'] = 'El comentario debe ser un texto.';
        }
        return $errors;
    }
}
