<?php

namespace itaxcix\Core\UseCases\Province;

use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use InvalidArgumentException;

class ProvinceDeleteUseCase
{
    private ProvinceRepositoryInterface $provinceRepository;
    private DistrictRepositoryInterface $districtRepository;

    public function __construct(ProvinceRepositoryInterface $provinceRepository, DistrictRepositoryInterface $districtRepository)
    {
        $this->provinceRepository = $provinceRepository;
        $this->districtRepository = $districtRepository;
    }

    public function execute(int $id): bool
    {
        $province = $this->provinceRepository->findById($id);
        if (!$province) {
            throw new InvalidArgumentException('Provincia no encontrada');
        }

        // Verificar si la provincia está asociada a algún distrito
        $districts = $this->districtRepository->findByProvinceId($id);
        if ($districts) {
            throw new InvalidArgumentException('No se puede eliminar la provincia porque está asociada a uno o más distritos.');
        }

        return $this->provinceRepository->delete($id);
    }
}
