<?php

namespace itaxcix\Core\Handler\DocumentType;

use itaxcix\Core\UseCases\DocumentType\DocumentTypeListUseCase;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypePaginationRequestDTO;

class DocumentTypeListUseCaseHandler
{
    private DocumentTypeListUseCase $useCase;

    public function __construct(DocumentTypeListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(DocumentTypePaginationRequestDTO $dto): array
    {
        return $this->useCase->execute($dto);
    }
}
