<?php

namespace itaxcix\Core\UseCases\Department;

use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;

class DepartmentDeleteUseCase
{
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(int $id): bool
    {
        $existingDepartment = $this->departmentRepository->findById($id);
        if (!$existingDepartment) {
            throw new \InvalidArgumentException('Department not found');
        }

        return $this->departmentRepository->delete($id);
    }
}
