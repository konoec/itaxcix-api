<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\CitizenProfileModel;

interface CitizenProfileRepositoryInterface {
    public function saveCitizenProfile(CitizenProfileModel $citizenProfileModel): CitizenProfileModel;
    public function findCitizenProfileByUserId(int $userId): ?CitizenProfileModel;
}