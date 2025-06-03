<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\DriverProfileModel;

interface DriverProfileRepositoryInterface {
    public function saveDriverProfile(DriverProfileModel $driverProfileModel): DriverProfileModel;
}