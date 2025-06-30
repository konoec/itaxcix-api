<?php

namespace itaxcix\Core\Handler\ContactType;

use itaxcix\Core\UseCases\ContactType\ContactTypeDeleteUseCase;

class ContactTypeDeleteUseCaseHandler
{
    private ContactTypeDeleteUseCase $useCase;

    public function __construct(ContactTypeDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}
