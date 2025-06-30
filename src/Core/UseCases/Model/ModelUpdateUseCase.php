<?php

namespace itaxcix\Core\UseCases\Model;

use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Model\ModelRequestDTO;
use itaxcix\Shared\DTO\useCases\Model\ModelResponseDTO;

class ModelUpdateUseCase
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

    public function execute(int $id, ModelRequestDTO $requestDTO): ModelResponseDTO
    {
        $existingModel = $this->modelRepository->findById($id);
        if (!$existingModel) {
            throw new \InvalidArgumentException('El modelo especificado no existe');
        }

        $brand = null;
        if ($requestDTO->getBrandId() !== null) {
            $brand = $this->brandRepository->findById($requestDTO->getBrandId());
        }

        $existingModel->setName($requestDTO->getName());
        $existingModel->setBrand($brand);
        $existingModel->setActive($requestDTO->isActive());

        $updatedModel = $this->modelRepository->update($existingModel);

        return new ModelResponseDTO(
            $updatedModel->getId(),
            $updatedModel->getName(),
            $updatedModel->getBrand()?->getId(),
            $updatedModel->getBrand()?->getName(),
            $updatedModel->isActive()
        );
    }
}
