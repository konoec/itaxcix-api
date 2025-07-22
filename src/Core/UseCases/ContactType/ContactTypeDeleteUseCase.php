<?php

namespace itaxcix\Core\UseCases\ContactType;

use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;

class ContactTypeDeleteUseCase
{
    private ContactTypeRepositoryInterface $contactTypeRepository;
    private UserContactRepositoryInterface $userContactRepository;

    public function __construct(ContactTypeRepositoryInterface $contactTypeRepository, UserContactRepositoryInterface $userContactRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
    }

    public function execute(int $id): bool
    {
        $existingContactType = $this->contactTypeRepository->findById($id);
        if (!$existingContactType) {
            return false;
        }

        // Verificar si el tipo de contacto es uno de los tipos especiales
        if (in_array($existingContactType->getName(), ['CORREO ELECTRÓNICO', 'TELÉFONO MÓVIL'])) {
            throw new \InvalidArgumentException('No se puede eliminar el tipo de contacto: ' . $existingContactType->getName());
        }

        // Verificar si el tipo de contacto está asociado a algún usuario
        $usersWithContactType = $this->userContactRepository->findByContactTypeId($id);
        if ($usersWithContactType) {
            throw new \InvalidArgumentException('No se puede eliminar el tipo de contacto porque está asociado a uno o más usuarios.');
        }

        return $this->contactTypeRepository->delete($id);
    }
}
