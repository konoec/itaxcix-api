<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\DriverProfileModel;

interface DriverProfileRepositoryInterface {
    public function findDriverProfileByUserId(int $userId): ?DriverProfileModel;
    public function findDriversProfilesByStatusId(int $statusId): array;
    public function saveDriverProfile(DriverProfileModel $driverProfileModel): DriverProfileModel;
}