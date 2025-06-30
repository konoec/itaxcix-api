<?php

namespace itaxcix\Core\Handler\ContactType;

use itaxcix\Core\UseCases\ContactType\ContactTypeListUseCase;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypePaginationRequestDTO;

class ContactTypeListUseCaseHandler
{
    private ContactTypeListUseCase $useCase;

    public function __construct(ContactTypeListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ContactTypePaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}
