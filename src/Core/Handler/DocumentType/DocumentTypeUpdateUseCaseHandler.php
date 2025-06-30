<?php

namespace itaxcix\Core\Handler\DocumentType;

use itaxcix\Core\UseCases\DocumentType\DocumentTypeUpdateUseCase;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeResponseDTO;

class DocumentTypeUpdateUseCaseHandler
{
    private DocumentTypeUpdateUseCase $useCase;

    public function __construct(DocumentTypeUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, DocumentTypeRequestDTO $dto): DocumentTypeResponseDTO
    {
        return $this->useCase->execute($id, $dto);
    }
}
