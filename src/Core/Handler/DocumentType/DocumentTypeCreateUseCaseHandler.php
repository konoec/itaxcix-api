<?php

namespace itaxcix\Core\Handler\DocumentType;

use itaxcix\Core\UseCases\DocumentType\DocumentTypeCreateUseCase;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeResponseDTO;

class DocumentTypeCreateUseCaseHandler
{
    private DocumentTypeCreateUseCase $useCase;

    public function __construct(DocumentTypeCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(DocumentTypeRequestDTO $dto): DocumentTypeResponseDTO
    {
        return $this->useCase->execute($dto);
    }
}
