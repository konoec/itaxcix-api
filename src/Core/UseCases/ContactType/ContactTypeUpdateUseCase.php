<?php

namespace itaxcix\Core\UseCases\ContactType;

use itaxcix\Core\Domain\user\ContactTypeModel;
use itaxcix\Core\Interfaces\User\ContactTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeResponseDTO;

class ContactTypeUpdateUseCase
{
    private ContactTypeRepositoryInterface $contactTypeRepository;

    public function __construct(ContactTypeRepositoryInterface $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
    }

    public function execute(int $id, ContactTypeRequestDTO $request): ?ContactTypeResponseDTO
    {
        $existingContactType = $this->contactTypeRepository->findById($id);
        if (!$existingContactType) {
            return null;
        }

        $contactType = new ContactTypeModel(
            $id,
            trim($request->name),
            $request->active
        );

        $updatedContactType = $this->contactTypeRepository->update($contactType);

        return new ContactTypeResponseDTO(
            $updatedContactType->getId(),
            $updatedContactType->getName(),
            $updatedContactType->isActive()
        );
    }
}
