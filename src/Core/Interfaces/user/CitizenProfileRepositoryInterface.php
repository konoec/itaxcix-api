<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\CitizenProfileModel;

interface CitizenProfileRepositoryInterface {
    public function saveCitizenProfile(CitizenProfileModel $citizenProfileModel): CitizenProfileModel;
}