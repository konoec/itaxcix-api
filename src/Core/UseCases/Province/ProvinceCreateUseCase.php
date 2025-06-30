<?php

namespace itaxcix\Core\UseCases\Province;

use itaxcix\Core\Domain\location\ProvinceModel;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Province\ProvinceRequestDTO;
use itaxcix\Shared\DTO\useCases\Province\ProvinceResponseDTO;

class ProvinceCreateUseCase
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

    public function execute(ProvinceRequestDTO $request): ProvinceResponseDTO
    {
        $department = $this->departmentRepository->findById($request->getDepartmentId());

        $province = new ProvinceModel(
            null,
            $request->getName(),
            $department,
            $request->getUbigeo()
        );

        $createdProvince = $this->provinceRepository->create($province);

        return ProvinceResponseDTO::fromModel($createdProvince);
    }
}
