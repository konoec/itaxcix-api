<?php

namespace itaxcix\Core\UseCases\Brand;

use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;

class BrandDeleteUseCase
{
    private BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function execute(int $id): bool
    {
        $existingBrand = $this->brandRepository->findById($id);

        if (!$existingBrand) {
            throw new \InvalidArgumentException('Marca no encontrada');
        }

        return $this->brandRepository->delete($id);
    }
}
