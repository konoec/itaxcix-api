<?php

namespace itaxcix\Core\UseCases\Model;

use itaxcix\Core\Domain\vehicle\ModelModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Model\ModelRequestDTO;
use itaxcix\Shared\DTO\useCases\Model\ModelResponseDTO;

class ModelCreateUseCase
{
    private ModelRepositoryInterface $modelRepository;
    private BrandRepositoryInterface $brandRepository;

    public function __construct(
        ModelRepositoryInterface $modelRepository,
        BrandRepositoryInterface $brandRepository
    ) {
        $this->modelRepository = $modelRepository;
        $this->brandRepository = $brandRepository;
    }

    public function execute(ModelRequestDTO $requestDTO): ModelResponseDTO
    {
        $brand = null;
        if ($requestDTO->getBrandId() !== null) {
            $brand = $this->brandRepository->findById($requestDTO->getBrandId());
        }

        $model = new ModelModel(
            null,
            $requestDTO->getName(),
            $brand,
            $requestDTO->isActive()
        );

        $createdModel = $this->modelRepository->create($model);

        return new ModelResponseDTO(
            $createdModel->getId(),
            $createdModel->getName(),
            $createdModel->getBrand()?->getId(),
            $createdModel->getBrand()?->getName(),
            $createdModel->isActive()
        );
    }
}
