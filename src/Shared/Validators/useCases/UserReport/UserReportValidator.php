<?php

namespace itaxcix\Shared\Validators\useCases\UserReport;

class UserReportValidator
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

        // Validar fechas de validación
        if (!empty($data['validationStartDate']) && !$this->isValidDate($data['validationStartDate'])) {
            $errors['validationStartDate'] = 'La fecha de inicio de validación no es válida (formato esperado: YYYY-MM-DD).';
        }
        if (!empty($data['validationEndDate']) && !$this->isValidDate($data['validationEndDate'])) {
            $errors['validationEndDate'] = 'La fecha de fin de validación no es válida (formato esperado: YYYY-MM-DD).';
        }
        if (!empty($data['validationStartDate']) && !empty($data['validationEndDate']) && $this->isValidDate($data['validationStartDate']) && $this->isValidDate($data['validationEndDate'])) {
            if (strtotime($data['validationStartDate']) > strtotime($data['validationEndDate'])) {
                $errors['validationDateRange'] = 'La fecha de inicio de validación no puede ser mayor que la fecha de fin.';
            }
        }

        // Validar IDs
        foreach (['documentTypeId', 'statusId'] as $idField) {
            if (isset($data[$idField]) && (!is_numeric($data[$idField]) || (int)$data[$idField] < 1)) {
                $errors[$idField] = 'El campo ' . $idField . ' debe ser un entero positivo.';
            }
        }

        // Validar sortBy y sortDirection
        $allowedSortBy = ['name', 'lastName', 'document', 'email', 'phone', 'validationDate'];
        if (isset($data['sortBy']) && !in_array($data['sortBy'], $allowedSortBy)) {
            $errors['sortBy'] = 'El campo sortBy no es válido.';
        }
        if (isset($data['sortDirection']) && !in_array(strtoupper($data['sortDirection']), ['ASC', 'DESC'])) {
            $errors['sortDirection'] = 'El campo sortDirection debe ser ASC o DESC.';
        }

        // Validar email si se envía
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido.';
        }

        // Validar phone si se envía (opcional, solo numérico)
        if (!empty($data['phone']) && !preg_match('/^[0-9\-\+\s]+$/', $data['phone'])) {
            $errors['phone'] = 'El teléfono solo puede contener números, espacios, guiones o +.';
        }

        return $errors;
    }

    private function isValidDate($date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}

