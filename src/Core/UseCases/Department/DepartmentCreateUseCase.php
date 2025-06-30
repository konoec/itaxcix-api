<?php

namespace itaxcix\Core\UseCases\Department;

use itaxcix\Core\Domain\location\DepartmentModel;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Department\DepartmentRequestDTO;
use itaxcix\Shared\DTO\useCases\Department\DepartmentResponseDTO;

class DepartmentCreateUseCase
{
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(DepartmentRequestDTO $request): DepartmentResponseDTO
    {
        $departmentModel = new DepartmentModel(
            null,
            $request->name,
            $request->ubigeo
        );

        $createdDepartment = $this->departmentRepository->create($departmentModel);

        return new DepartmentResponseDTO(
            $createdDepartment->getId(),
            $createdDepartment->getName(),
            $createdDepartment->getUbigeo()
        );
    }
}
