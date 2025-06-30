<?php

namespace itaxcix\Core\Handler\DocumentType;

use itaxcix\Core\UseCases\DocumentType\DocumentTypeDeleteUseCase;

class DocumentTypeDeleteUseCaseHandler
{
    private DocumentTypeDeleteUseCase $useCase;

    public function __construct(DocumentTypeDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
