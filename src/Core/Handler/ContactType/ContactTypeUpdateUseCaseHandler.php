<?php

namespace itaxcix\Core\Handler\ContactType;

use itaxcix\Core\UseCases\ContactType\ContactTypeUpdateUseCase;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeResponseDTO;

class ContactTypeUpdateUseCaseHandler
{
    private ContactTypeUpdateUseCase $useCase;

    public function __construct(ContactTypeUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, ContactTypeRequestDTO $request): ?ContactTypeResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}
