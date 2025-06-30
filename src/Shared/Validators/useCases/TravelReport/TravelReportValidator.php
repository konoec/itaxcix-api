<?php

namespace itaxcix\Shared\Validators\useCases\TravelReport;

class TravelReportValidator
{
    public function validateFilters(array $data): array
    {
        $errors = [];

        // Validar paginación
        if (isset($data['page']) && (!is_numeric($data['page']) || (int)$data['page'] < 1)) {
            $errors['page'] = 'El número de página debe ser un entero positivo.';
        }
        if (isset($data['perPage']) && (!is_numeric($data['perPage']) || (int)$data['perPage'] < 1 || (int)$data['perPage'] > 100)) {
            $errors['perPage'] = 'El tamaño de página debe ser un entero entre 1 y 100.';
        }

        // Validar fechas
        if (!empty($data['startDate']) && !$this->isValidDate($data['startDate'])) {
            $errors['startDate'] = 'La fecha de inicio no es válida (formato esperado: YYYY-MM-DD).';
        }
        if (!empty($data['endDate']) && !$this->isValidDate($data['endDate'])) {
            $errors['endDate'] = 'La fecha de fin no es válida (formato esperado: YYYY-MM-DD).';
        }
        if (!empty($data['startDate']) && !empty($data['endDate']) && $this->isValidDate($data['startDate']) && $this->isValidDate($data['endDate'])) {
            if (strtotime($data['startDate']) > strtotime($data['endDate'])) {
                $errors['dateRange'] = 'La fecha de inicio no puede ser mayor que la fecha de fin.';
            }
        }

        // Validar IDs
        foreach (['citizenId', 'driverId', 'statusId'] as $idField) {
            if (isset($data[$idField]) && (!is_numeric($data[$idField]) || (int)$data[$idField] < 1)) {
                $errors[$idField] = 'El campo ' . $idField . ' debe ser un entero positivo.';
            }
        }

        // Validar sortBy y sortDirection
        $allowedSortBy = ['creationDate', 'startDate', 'endDate', 'citizenId', 'driverId', 'statusId'];
        if (isset($data['sortBy']) && !in_array($data['sortBy'], $allowedSortBy)) {
            $errors['sortBy'] = 'El campo sortBy no es válido.';
        }
        if (isset($data['sortDirection']) && !in_array(strtoupper($data['sortDirection']), ['ASC', 'DESC'])) {
            $errors['sortDirection'] = 'El campo sortDirection debe ser ASC o DESC.';
        }

        return $errors;
    }

    private function isValidDate($date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}

