<?php

namespace itaxcix\services;

use itaxcix\mocks\MunicipalidadMockData;

class MunicipalidadService {

    /**
     * Retrieves vehicle data from the mock data source based on the provided plate number.
     *
     * @param string $plate The plate number of the vehicle.
     * @return array|null The vehicle data if found, null otherwise.
     */
    public function getVehicleTUC(string $plate): ?array {
        $vehicleData = MunicipalidadMockData::getVehicleByPlate($plate);
        return $vehicleData ?? null;
    }
}