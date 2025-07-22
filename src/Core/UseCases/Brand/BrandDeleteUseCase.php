<?php

namespace itaxcix\Core\UseCases\Brand;

use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;

class BrandDeleteUseCase
{
    private BrandRepositoryInterface $brandRepository;
    private ModelRepositoryInterface $modelRepository;

    public function __construct(BrandRepositoryInterface $brandRepository, ModelRepositoryInterface $modelRepository)
    {
        $this->brandRepository = $brandRepository;
        $this->modelRepository = $modelRepository;
    }

    public function execute(int $id): bool
    {
        $existingBrand = $this->brandRepository->findById($id);

        if (!$existingBrand) {
            throw new \InvalidArgumentException('Marca no encontrada');
        }

        // Verificar si la marca tiene modelos asociados
        $models = $this->modelRepository->findActiveByBrandId($id);
        if (!empty($models)) {
            throw new \InvalidArgumentException('No se puede eliminar la marca porque tiene modelos asociados.');
        }

        return $this->brandRepository->delete($id);
    }
}
