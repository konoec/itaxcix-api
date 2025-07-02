<?php

namespace itaxcix\Shared\Validators\useCases\Incident;

use itaxcix\Shared\Validators\generic\BaseValidator;

class GetUserIncidentsValidator extends BaseValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validar userId (requerido)
        if (!isset($data['userId'])) {
            $errors['userId'] = 'El ID del usuario es requerido';
        } elseif (!is_numeric($data['userId']) || (int)$data['userId'] <= 0) {
            $errors['userId'] = 'El ID del usuario debe ser un número entero positivo';
        }

        // Validar travelId (opcional)
        if (isset($data['travelId']) && (!is_numeric($data['travelId']) || (int)$data['travelId'] <= 0)) {
            $errors['travelId'] = 'El ID del viaje debe ser un número entero positivo';
        }

        // Validar typeId (opcional)
        if (isset($data['typeId']) && (!is_numeric($data['typeId']) || (int)$data['typeId'] <= 0)) {
            $errors['typeId'] = 'El ID del tipo de incidencia debe ser un número entero positivo';
        }

        // Validar active (opcional)
        if (isset($data['active']) && !is_bool($data['active']) && !in_array($data['active'], ['true', 'false', '1', '0', 1, 0], true)) {
            $errors['active'] = 'El campo active debe ser un valor booleano';
        }

        // Validar comment (opcional)
        if (isset($data['comment']) && (!is_string($data['comment']) || strlen($data['comment']) > 255)) {
            $errors['comment'] = 'El comentario debe ser una cadena de texto de máximo 255 caracteres';
        }

        // Validar paginación
        $paginationErrors = $this->validatePagination($data);
        $errors = array_merge($errors, $paginationErrors);

        // Validar ordenamiento
        $allowedSortFields = ['id', 'travelId', 'typeId', 'active'];
        if (isset($data['sortBy']) && !in_array($data['sortBy'], $allowedSortFields)) {
            $errors['sortBy'] = 'El campo de ordenamiento debe ser uno de: ' . implode(', ', $allowedSortFields);
        }

        if (isset($data['sortDirection']) && !in_array(strtoupper($data['sortDirection']), ['ASC', 'DESC'])) {
            $errors['sortDirection'] = 'La dirección de ordenamiento debe ser ASC o DESC';
        }

        return $errors;
    }
}
