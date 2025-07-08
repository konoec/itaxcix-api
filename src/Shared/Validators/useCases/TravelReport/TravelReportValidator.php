<?php

namespace itaxcix\Shared\Validators\useCases\TravelReport;

class TravelReportValidator
{
    public function validateFilters(array $data): array
    {
        $errors = [];

        // Validar página
        if (isset($data['page'])) {
            if (!is_numeric($data['page']) || (int)$data['page'] < 1) {
                $errors['page'] = 'La página debe ser un número entero mayor a 0';
            }
        }

        // Validar elementos por página
        if (isset($data['perPage'])) {
            $perPage = (int)$data['perPage'];
            if (!is_numeric($data['perPage']) || $perPage < 1 || $perPage > 100) {
                $errors['perPage'] = 'Los elementos por página deben estar entre 1 y 100';
            }
        }

        // Validar fechas
        if (isset($data['startDate']) && !empty($data['startDate'])) {
            if (!$this->isValidDate($data['startDate'])) {
                $errors['startDate'] = 'La fecha de inicio debe tener el formato YYYY-MM-DD';
            }
        }

        if (isset($data['endDate']) && !empty($data['endDate'])) {
            if (!$this->isValidDate($data['endDate'])) {
                $errors['endDate'] = 'La fecha de fin debe tener el formato YYYY-MM-DD';
            }
        }

        // Validar que la fecha de inicio sea menor o igual a la fecha de fin
        if (isset($data['startDate']) && isset($data['endDate']) &&
            !empty($data['startDate']) && !empty($data['endDate'])) {
            if (strtotime($data['startDate']) > strtotime($data['endDate'])) {
                $errors['dateRange'] = 'La fecha de inicio debe ser menor o igual a la fecha de fin';
            }
        }

        // Validar IDs
        if (isset($data['citizenId']) && !empty($data['citizenId'])) {
            if (!is_numeric($data['citizenId']) || (int)$data['citizenId'] < 1) {
                $errors['citizenId'] = 'El ID del ciudadano debe ser un número entero positivo';
            }
        }

        if (isset($data['driverId']) && !empty($data['driverId'])) {
            if (!is_numeric($data['driverId']) || (int)$data['driverId'] < 1) {
                $errors['driverId'] = 'El ID del conductor debe ser un número entero positivo';
            }
        }

        if (isset($data['statusId']) && !empty($data['statusId'])) {
            if (!is_numeric($data['statusId']) || (int)$data['statusId'] < 1) {
                $errors['statusId'] = 'El ID del estado debe ser un número entero positivo';
            }
        }

        // Validar ordenamiento
        if (isset($data['sortBy'])) {
            $allowedSortFields = ['creationDate', 'startDate', 'endDate', 'citizenId', 'driverId', 'statusId'];
            if (!in_array($data['sortBy'], $allowedSortFields)) {
                $errors['sortBy'] = 'El campo de ordenamiento debe ser uno de: ' . implode(', ', $allowedSortFields);
            }
        }

        if (isset($data['sortDirection'])) {
            $allowedDirections = ['ASC', 'DESC'];
            if (!in_array(strtoupper($data['sortDirection']), $allowedDirections)) {
                $errors['sortDirection'] = 'La dirección de ordenamiento debe ser ASC o DESC';
            }
        }

        // Validar longitud de strings
        if (isset($data['origin']) && !empty($data['origin'])) {
            if (strlen($data['origin']) > 255) {
                $errors['origin'] = 'El origen no puede tener más de 255 caracteres';
            }
        }

        if (isset($data['destination']) && !empty($data['destination'])) {
            if (strlen($data['destination']) > 255) {
                $errors['destination'] = 'El destino no puede tener más de 255 caracteres';
            }
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
