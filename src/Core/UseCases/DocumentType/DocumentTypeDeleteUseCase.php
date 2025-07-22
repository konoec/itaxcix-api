<?php

namespace itaxcix\Core\UseCases\DocumentType;

use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;

class DocumentTypeDeleteUseCase
{
    private DocumentTypeRepositoryInterface $repository;
    private PersonRepositoryInterface $personRepository;

    public function __construct(DocumentTypeRepositoryInterface $repository, PersonRepositoryInterface $personRepository)
    {
        $this->repository = $repository;
        $this->personRepository = $personRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que el tipo de documento existe
        $existingModel = $this->repository->findById($id);
        if (!$existingModel) {
            throw new \InvalidArgumentException("Tipo de documento con ID {$id} no encontrado");
        }

        // Verificar que no sea un tipo de documento crítico del sistema
        if ($existingModel->getName() === 'DNI' || $existingModel->getName() === 'PASAPORTE' || $existingModel->getName() === 'CARNÉ DE EXTRANGERÍA' || $existingModel->getName() === 'RUC') {
            throw new \InvalidArgumentException("No se puede eliminar el tipo de documento: " . $existingModel->getName());
        }

        // Verificar si el tipo de documento está asociado a alguna persona
        $personsWithDocumentType = $this->personRepository->findByDocumentTypeId($id);
        if ($personsWithDocumentType) {
            throw new \InvalidArgumentException('No se puede eliminar el tipo de documento porque está asociado a una o más personas.');
        }

        return $this->repository->delete($id);
    }
}
