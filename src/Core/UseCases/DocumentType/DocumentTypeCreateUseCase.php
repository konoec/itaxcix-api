<?php

namespace itaxcix\Core\UseCases\DocumentType;

use itaxcix\Core\Domain\person\DocumentTypeModel;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeResponseDTO;

class DocumentTypeCreateUseCase
{
    private DocumentTypeRepositoryInterface $repository;

    public function __construct(DocumentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DocumentTypeRequestDTO $dto): DocumentTypeResponseDTO
    {
        // Verificar que no exista otro tipo de documento con el mismo nombre
        if ($this->repository->existsByName($dto->getName())) {
            throw new \InvalidArgumentException("Ya existe un tipo de documento con el nombre '{$dto->getName()}'");
        }

        $model = new DocumentTypeModel(
            0, // ID será asignado por la base de datos
            $dto->getName(),
            $dto->getActive() ?? true
        );

        $createdModel = $this->repository->create($model);

        return new DocumentTypeResponseDTO(
            $createdModel->getId(),
            $createdModel->getName(),
            $createdModel->isActive()
        );
    }
}
