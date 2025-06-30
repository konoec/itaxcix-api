<?php

namespace itaxcix\Shared\Validators\useCases\VehicleReport;

class VehicleReportValidator
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

        // Placa
        if (isset($data['licensePlate']) && !empty($data['licensePlate']) && !preg_match('/^[A-Z0-9-]{4,10}$/i', $data['licensePlate'])) {
            $errors['licensePlate'] = 'La placa debe tener entre 4 y 10 caracteres alfanuméricos.';
        }

        // IDs
        foreach ([
            'brandId', 'modelId', 'colorId', 'fuelTypeId', 'vehicleClassId', 'categoryId',
            'companyId', 'districtId', 'statusId', 'procedureTypeId', 'modalityId'
        ] as $idField) {
            if (isset($data[$idField]) && (!is_numeric($data[$idField]) || (int)$data[$idField] < 1)) {
                $errors[$idField] = 'El campo ' . $idField . ' debe ser un entero positivo.';
            }
        }

        // manufactureYearFrom y manufactureYearTo
        if (isset($data['manufactureYearFrom']) && (!is_numeric($data['manufactureYearFrom']) || (int)$data['manufactureYearFrom'] < 1900)) {
            $errors['manufactureYearFrom'] = 'El año de fabricación inicial debe ser un año válido.';
        }
        if (isset($data['manufactureYearTo']) && (!is_numeric($data['manufactureYearTo']) || (int)$data['manufactureYearTo'] < 1900)) {
            $errors['manufactureYearTo'] = 'El año de fabricación final debe ser un año válido.';
        }
        if (isset($data['manufactureYearFrom'], $data['manufactureYearTo']) && is_numeric($data['manufactureYearFrom']) && is_numeric($data['manufactureYearTo'])) {
            if ((int)$data['manufactureYearFrom'] > (int)$data['manufactureYearTo']) {
                $errors['manufactureYearRange'] = 'El año de fabricación inicial no puede ser mayor que el final.';
            }
        }

        // seatCount y passengerCount
        if (isset($data['seatCount']) && (!is_numeric($data['seatCount']) || (int)$data['seatCount'] < 1)) {
            $errors['seatCount'] = 'El número de asientos debe ser un entero positivo.';
        }
        if (isset($data['passengerCount']) && (!is_numeric($data['passengerCount']) || (int)$data['passengerCount'] < 1)) {
            $errors['passengerCount'] = 'El número de pasajeros debe ser un entero positivo.';
        }

        // sortBy y sortDirection
        $allowedSortBy = [
            'licensePlate', 'manufactureYear', 'seatCount', 'passengerCount',
            'brandId', 'modelId', 'colorId', 'fuelTypeId', 'vehicleClassId', 'categoryId',
            'companyId', 'districtId', 'statusId', 'procedureTypeId', 'modalityId'
        ];
        if (isset($data['sortBy']) && !in_array($data['sortBy'], $allowedSortBy)) {
            $errors['sortBy'] = 'El campo sortBy no es válido.';
        }
        if (isset($data['sortDirection']) && !in_array(strtoupper($data['sortDirection']), ['ASC', 'DESC'])) {
            $errors['sortDirection'] = 'El campo sortDirection debe ser ASC o DESC.';
        }

        return $errors;
    }
}

