<?php

namespace itaxcix\Shared\Validators\useCases\AuditLog;

class AuditLogValidator
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

        // affectedTable, operation, systemUser (opcional, solo longitud)
        if (isset($data['affectedTable']) && strlen($data['affectedTable']) > 100) {
            $errors['affectedTable'] = 'El nombre de la tabla afectada no puede exceder 100 caracteres.';
        }
        if (isset($data['operation']) && strlen($data['operation']) > 20) {
            $errors['operation'] = 'La operación no puede exceder 20 caracteres.';
        }
        if (isset($data['systemUser']) && strlen($data['systemUser']) > 100) {
            $errors['systemUser'] = 'El usuario de sistema no puede exceder 100 caracteres.';
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

        // sortBy y sortDirection
        $allowedSortBy = ['date', 'affectedTable', 'operation', 'systemUser'];
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

