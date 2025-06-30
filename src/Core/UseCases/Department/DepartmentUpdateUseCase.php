<?php

namespace itaxcix\Core\UseCases\Department;

use itaxcix\Core\Domain\location\DepartmentModel;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Department\DepartmentRequestDTO;
use itaxcix\Shared\DTO\useCases\Department\DepartmentResponseDTO;

class DepartmentUpdateUseCase
{
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(DepartmentRequestDTO $request): DepartmentResponseDTO
    {
        $existingDepartment = $this->departmentRepository->findById($request->id);
        if (!$existingDepartment) {
            throw new \InvalidArgumentException('Department not found');
        }

        $departmentModel = new DepartmentModel(
            $request->id,
            $request->name,
            $request->ubigeo
        );

        $updatedDepartment = $this->departmentRepository->update($departmentModel);

        return new DepartmentResponseDTO(
            $updatedDepartment->getId(),
            $updatedDepartment->getName(),
            $updatedDepartment->getUbigeo()
        );
    }
}
