<?php

namespace itaxcix\Shared\Validators\useCases\InfractionReport;

class InfractionReportValidator
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
        foreach ([ 'userId', 'severityId', 'statusId' ] as $idField) {
            if (isset($data[$idField]) && (!is_numeric($data[$idField]) || (int)$data[$idField] < 1)) {
                $errors[$idField] = 'El campo ' . $idField . ' debe ser un entero positivo.';
            }
        }

        // Fechas
        if (!empty($data['dateFrom']) && !$this->isValidDate($data['dateFrom'])) {
            $errors['dateFrom'] = 'La fecha inicial no es válida (formato esperado: YYYY-MM-DD).';
        }
        if (!empty($data['dateTo']) && !$this->isValidDate($data['dateTo'])) {
            $errors['dateTo'] = 'La fecha final no es válida (formato esperado: YYYY-MM-DD).';
        }
        if (!empty($data['dateFrom']) && !empty($data['dateTo']) && $this->isValidDate($data['dateFrom']) && $this->isValidDate($data['dateTo'])) {
            if (strtotime($data['dateFrom']) > strtotime($data['dateTo'])) {
                $errors['dateRange'] = 'La fecha inicial no puede ser mayor que la fecha final.';
            }
        }

        // Descripción (opcional, solo longitud)
        if (isset($data['description']) && strlen($data['description']) > 255) {
            $errors['description'] = 'La descripción no puede exceder 255 caracteres.';
        }

        // sortBy y sortDirection
        $allowedSortBy = ['id', 'userId', 'severityId', 'statusId', 'date'];
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

