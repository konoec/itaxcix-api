<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\AdminProfileModel;

interface AdminProfileRepositoryInterface {
    public function saveAdminProfile(AdminProfileModel $adminProfileModel): AdminProfileModel;
}