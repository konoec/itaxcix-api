<?php

namespace itaxcix\Core\Handler\Department;

use itaxcix\Core\UseCases\Department\DepartmentListUseCase;
use itaxcix\Shared\DTO\useCases\Department\DepartmentPaginationRequestDTO;

class DepartmentListUseCaseHandler
{
    private DepartmentListUseCase $departmentListUseCase;

    public function __construct(DepartmentListUseCase $departmentListUseCase)
    {
        $this->departmentListUseCase = $departmentListUseCase;
    }

    public function handle(DepartmentPaginationRequestDTO $request): array
    {
        return $this->departmentListUseCase->execute($request);
    }
}
