<?php

namespace itaxcix\Core\Handler\Department;

use itaxcix\Core\UseCases\Department\DepartmentDeleteUseCase;

class DepartmentDeleteUseCaseHandler
{
    private DepartmentDeleteUseCase $departmentDeleteUseCase;

    public function __construct(DepartmentDeleteUseCase $departmentDeleteUseCase)
    {
        $this->departmentDeleteUseCase = $departmentDeleteUseCase;
    }

    public function handle(int $id): bool
    {
        return $this->departmentDeleteUseCase->execute($id);
    }
}
