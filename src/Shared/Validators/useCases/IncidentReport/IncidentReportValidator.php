<?php

namespace itaxcix\Shared\Validators\useCases\IncidentReport;

class IncidentReportValidator
{
    public function validateFilters(array $data): array
    {
        $errors = [];

        // Paginación
        if (isset($data['page']) && (!is_numeric($data['page']) || (int)$data['page'] < 1)) {
            $errors['page'] = 'El número de página debe ser un entero positivo.';
        }
        if (isset($data['perPage']) && (!is_numeric($data['perPage']) || (int)$data['perPage'] < 1 || (int)$data['perPage'] > 100)) {
            $errors['perPage'] = 'El tamaño de página debe ser un entero entre 1 y 100.';
        }

        // IDs
        foreach ([ 'userId', 'travelId', 'typeId' ] as $idField) {
            if (isset($data[$idField]) && (!is_numeric($data[$idField]) || (int)$data[$idField] < 1)) {
                $errors[$idField] = 'El campo ' . $idField . ' debe ser un entero positivo.';
            }
        }

        // Estado activo
        if (isset($data['active']) && !in_array($data['active'], [true, false, 'true', 'false', 1, 0, '1', '0'], true)) {
            $errors['active'] = 'El campo active debe ser booleano.';
        }

        // Comentario (opcional, solo longitud)
        if (isset($data['comment']) && strlen($data['comment']) > 255) {
            $errors['comment'] = 'El comentario no puede exceder 255 caracteres.';
        }

        // sortBy y sortDirection
        $allowedSortBy = ['id', 'userId', 'travelId', 'typeId', 'active'];
        if (isset($data['sortBy']) && !in_array($data['sortBy'], $allowedSortBy)) {
            $errors['sortBy'] = 'El campo sortBy no es válido.';
        }
        if (isset($data['sortDirection']) && !in_array(strtoupper($data['sortDirection']), ['ASC', 'DESC'])) {
            $errors['sortDirection'] = 'El campo sortDirection debe ser ASC o DESC.';
        }

        return $errors;
    }
}

