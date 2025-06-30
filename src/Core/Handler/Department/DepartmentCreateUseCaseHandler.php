<?php

namespace itaxcix\Core\Handler\Department;

use itaxcix\Core\UseCases\Department\DepartmentCreateUseCase;
use itaxcix\Shared\DTO\useCases\Department\DepartmentRequestDTO;
use itaxcix\Shared\DTO\useCases\Department\DepartmentResponseDTO;

class DepartmentCreateUseCaseHandler
{
    private DepartmentCreateUseCase $departmentCreateUseCase;

    public function __construct(DepartmentCreateUseCase $departmentCreateUseCase)
    {
        $this->departmentCreateUseCase = $departmentCreateUseCase;
    }

    public function handle(DepartmentRequestDTO $request): DepartmentResponseDTO
    {
        return $this->departmentCreateUseCase->execute($request);
    }
}
