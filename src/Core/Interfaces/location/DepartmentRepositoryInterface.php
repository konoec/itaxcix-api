<?php

namespace itaxcix\Core\Interfaces\location;

use itaxcix\Core\Domain\location\DepartmentModel;

interface DepartmentRepositoryInterface
{
    public function findDepartmentByName(string $name): ?DepartmentModel;
    public function saveDepartment(DepartmentModel $departmentModel): DepartmentModel;
}