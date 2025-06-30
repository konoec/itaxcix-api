<?php

namespace itaxcix\Core\UseCases\DocumentType;

use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypePaginationRequestDTO;

class DocumentTypeListUseCase
{
    private DocumentTypeRepositoryInterface $repository;

    public function __construct(DocumentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DocumentTypePaginationRequestDTO $dto): array
    {
        return $this->repository->findAll($dto);
    }
}
