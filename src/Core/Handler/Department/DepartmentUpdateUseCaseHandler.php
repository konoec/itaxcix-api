<?php

namespace itaxcix\Core\Handler\Department;

use itaxcix\Core\UseCases\Department\DepartmentUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Department\DepartmentRequestDTO;
use itaxcix\Shared\DTO\useCases\Department\DepartmentResponseDTO;

class DepartmentUpdateUseCaseHandler
{
    private DepartmentUpdateUseCase $departmentUpdateUseCase;

    public function __construct(DepartmentUpdateUseCase $departmentUpdateUseCase)
    {
        $this->departmentUpdateUseCase = $departmentUpdateUseCase;
    }

    public function handle(DepartmentRequestDTO $request): DepartmentResponseDTO
    {
        return $this->departmentUpdateUseCase->execute($request);
    }
}
