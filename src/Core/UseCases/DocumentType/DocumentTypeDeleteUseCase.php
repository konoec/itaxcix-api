<?php

namespace itaxcix\Core\UseCases\DocumentType;

use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;

class DocumentTypeDeleteUseCase
{
    private DocumentTypeRepositoryInterface $repository;

    public function __construct(DocumentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        // Verificar que el tipo de documento existe
        $existingModel = $this->repository->findById($id);
        if (!$existingModel) {
            throw new \InvalidArgumentException("Tipo de documento con ID {$id} no encontrado");
        }

        return $this->repository->delete($id);
    }
}
