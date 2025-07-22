<?php

namespace itaxcix\Core\UseCases\Department;

use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;

class DepartmentDeleteUseCase
{
    private DepartmentRepositoryInterface $departmentRepository;
    private ProvinceRepositoryInterface $provinceRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository, ProvinceRepositoryInterface $provinceRepository)
    {
        $this->departmentRepository = $departmentRepository;
        $this->provinceRepository = $provinceRepository;
    }

    public function execute(int $id): bool
    {
        $existingDepartment = $this->departmentRepository->findById($id);
        if (!$existingDepartment) {
            throw new \InvalidArgumentException('Departamento no encontrado');
        }

        // Verificar que no sea un departamento crítico del sistema
        if (in_array($existingDepartment->getName(), ['LAMBAYEQUE'])) {
            throw new \InvalidArgumentException('No se puede eliminar el departamento crítico: ' . $existingDepartment->getName());
        }

        // Verificar si el departamento está asociado a alguna provincia
        $provincesWithDepartment = $this->provinceRepository->findByDepartmentId($id);
        if ($provincesWithDepartment) {
            throw new \InvalidArgumentException('No se puede eliminar el departamento porque está asociado a una o más provincias.');
        }

        return $this->departmentRepository->delete($id);
    }
}
