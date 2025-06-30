<?php

namespace itaxcix\Core\UseCases\DocumentType;

use itaxcix\Core\Domain\person\DocumentTypeModel;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeResponseDTO;

class DocumentTypeUpdateUseCase
{
    private DocumentTypeRepositoryInterface $repository;

    public function __construct(DocumentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, DocumentTypeRequestDTO $dto): DocumentTypeResponseDTO
    {
        // Verificar que el tipo de documento existe
        $existingModel = $this->repository->findById($id);
        if (!$existingModel) {
            throw new \InvalidArgumentException("Tipo de documento con ID {$id} no encontrado");
        }

        // Verificar que no exista otro tipo de documento con el mismo nombre
        if ($this->repository->existsByName($dto->getName(), $id)) {
            throw new \InvalidArgumentException("Ya existe un tipo de documento con el nombre '{$dto->getName()}'");
        }

        $model = new DocumentTypeModel(
            $id,
            $dto->getName(),
            $dto->getActive() ?? $existingModel->isActive()
        );

        $updatedModel = $this->repository->update($model);

        return new DocumentTypeResponseDTO(
            $updatedModel->getId(),
            $updatedModel->getName(),
            $updatedModel->isActive()
        );
    }
}
