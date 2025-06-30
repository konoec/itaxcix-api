<?php

namespace itaxcix\Core\UseCases\ContactType;

use itaxcix\Core\Interfaces\User\ContactTypeRepositoryInterface;

class ContactTypeDeleteUseCase
{
    private ContactTypeRepositoryInterface $contactTypeRepository;

    public function __construct(ContactTypeRepositoryInterface $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
    }

    public function execute(int $id): bool
    {
        $existingContactType = $this->contactTypeRepository->findById($id);
        if (!$existingContactType) {
            return false;
        }

        return $this->contactTypeRepository->delete($id);
    }
}
