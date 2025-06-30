<?php

namespace itaxcix\Core\UseCases\Province;

use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use InvalidArgumentException;

class ProvinceDeleteUseCase
{
    private ProvinceRepositoryInterface $provinceRepository;

    public function __construct(ProvinceRepositoryInterface $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }

    public function execute(int $id): bool
    {
        $province = $this->provinceRepository->findById($id);
        if (!$province) {
            throw new InvalidArgumentException('Provincia no encontrada');
        }

        return $this->provinceRepository->delete($id);
    }
}
