<?php

namespace itaxcix\Core\Handler\ContactType;

use itaxcix\Core\UseCases\ContactType\ContactTypeCreateUseCase;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeResponseDTO;

class ContactTypeCreateUseCaseHandler
{
    private ContactTypeCreateUseCase $useCase;

    public function __construct(ContactTypeCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ContactTypeRequestDTO $request): ContactTypeResponseDTO
    {
        return $this->useCase->execute($request);
    }
}
