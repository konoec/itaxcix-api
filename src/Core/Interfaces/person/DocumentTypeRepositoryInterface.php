<?php

namespace itaxcix\Core\Interfaces\person;

use itaxcix\Core\Domain\person\DocumentTypeModel;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypePaginationRequestDTO;

interface DocumentTypeRepositoryInterface
{
    public function findDocumentTypeByName(string $name): ?DocumentTypeModel;

    // Métodos CRUD agregados
    public function findAll(DocumentTypePaginationRequestDTO $dto): array;
    public function findById(int $id): ?DocumentTypeModel;
    public function create(DocumentTypeModel $model): DocumentTypeModel;
    public function update(DocumentTypeModel $model): DocumentTypeModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
}