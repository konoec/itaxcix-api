<?php

namespace itaxcix\Core\UseCases\Province;

use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Province\ProvinceRequestDTO;
use itaxcix\Shared\DTO\useCases\Province\ProvinceResponseDTO;
use InvalidArgumentException;

class ProvinceUpdateUseCase
{
    private ProvinceRepositoryInterface $provinceRepository;
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(
        ProvinceRepositoryInterface $provinceRepository,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->provinceRepository = $provinceRepository;
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(int $id, ProvinceRequestDTO $request): ProvinceResponseDTO
    {
        $province = $this->provinceRepository->findById($id);
        if (!$province) {
            throw new InvalidArgumentException('Provincia no encontrada');
        }

        $department = $this->departmentRepository->findById($request->getDepartmentId());

        $province->setName($request->getName());
        $province->setDepartment($department);
        $province->setUbigeo($request->getUbigeo());

        $updatedProvince = $this->provinceRepository->update($province);

        return ProvinceResponseDTO::fromModel($updatedProvince);
    }
}
