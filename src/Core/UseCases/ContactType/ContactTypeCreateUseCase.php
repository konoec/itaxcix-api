<?php

namespace itaxcix\Core\UseCases\ContactType;

use itaxcix\Core\Domain\user\ContactTypeModel;
use itaxcix\Core\Interfaces\User\ContactTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ContactType\ContactTypeResponseDTO;

class ContactTypeCreateUseCase
{
    private ContactTypeRepositoryInterface $contactTypeRepository;

    public function __construct(ContactTypeRepositoryInterface $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
    }

    public function execute(ContactTypeRequestDTO $request): ContactTypeResponseDTO
    {
        $contactType = new ContactTypeModel(
            0, // El ID se asignará automáticamente
            trim($request->name),
            $request->active
        );

        $createdContactType = $this->contactTypeRepository->create($contactType);

        return new ContactTypeResponseDTO(
            $createdContactType->getId(),
            $createdContactType->getName(),
            $createdContactType->isActive()
        );
    }
}
