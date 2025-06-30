<?php

namespace itaxcix\Shared\Validators\useCases\RatingReport;

class RatingReportValidator
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
        foreach ([ 'raterId', 'ratedId', 'travelId' ] as $idField) {
            if (isset($data[$idField]) && (!is_numeric($data[$idField]) || (int)$data[$idField] < 1)) {
                $errors[$idField] = 'El campo ' . $idField . ' debe ser un entero positivo.';
            }
        }

        // Puntaje
        if (isset($data['minScore']) && (!is_numeric($data['minScore']) || (int)$data['minScore'] < 0)) {
            $errors['minScore'] = 'El puntaje mínimo debe ser un entero mayor o igual a 0.';
        }
        if (isset($data['maxScore']) && (!is_numeric($data['maxScore']) || (int)$data['maxScore'] < 0)) {
            $errors['maxScore'] = 'El puntaje máximo debe ser un entero mayor o igual a 0.';
        }
        if (isset($data['minScore'], $data['maxScore']) && is_numeric($data['minScore']) && is_numeric($data['maxScore'])) {
            if ((int)$data['minScore'] > (int)$data['maxScore']) {
                $errors['scoreRange'] = 'El puntaje mínimo no puede ser mayor que el máximo.';
            }
        }

        // Comentario (opcional, solo longitud)
        if (isset($data['comment']) && strlen($data['comment']) > 255) {
            $errors['comment'] = 'El comentario no puede exceder 255 caracteres.';
        }

        // sortBy y sortDirection
        $allowedSortBy = ['id', 'raterId', 'ratedId', 'travelId', 'score'];
        if (isset($data['sortBy']) && !in_array($data['sortBy'], $allowedSortBy)) {
            $errors['sortBy'] = 'El campo sortBy no es válido.';
        }
        if (isset($data['sortDirection']) && !in_array(strtoupper($data['sortDirection']), ['ASC', 'DESC'])) {
            $errors['sortDirection'] = 'El campo sortDirection debe ser ASC o DESC.';
        }

        return $errors;
    }
}

