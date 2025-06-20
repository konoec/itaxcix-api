<?php

namespace itaxcix\Core\Handler\Profile;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\AdminProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\UseCases\Profile\GetAdminProfileUseCase;
use itaxcix\Shared\DTO\useCases\Profile\AdminProfileResponseDTO;

class GetAdminProfileUseCaseHandler implements GetAdminProfileUseCase
{
    private AdminProfileRepositoryInterface $adminProfileRepository;
    private UserContactRepositoryInterface $userContactRepository;

    public function __construct(
        AdminProfileRepositoryInterface $adminProfileRepository,
        UserContactRepositoryInterface $userContactRepository
    ){
        $this->adminProfileRepository = $adminProfileRepository;
        $this->userContactRepository = $userContactRepository;
    }

    public function execute(int $userId): ?AdminProfileResponseDTO
    {
        $profile = $this->adminProfileRepository->findAdminProfileByUserId($userId);

        if (!$profile) {
            throw new InvalidArgumentException('El usuario no tiene un perfil de administrador.');
        }

        $email = $this->userContactRepository->findUserContactByUserIdAndContactTypeId($userId, 1);
        $phone = $this->userContactRepository->findUserContactByUserIdAndContactTypeId($userId, 2);

        return new AdminProfileResponseDTO(
            firstName: $profile->getUser()->getPerson()->getName(),
            lastName: $profile->getUser()->getPerson()->getLastName(),
            documentType: $profile->getUser()->getPerson()->getDocumentType()->getName(),
            document: $profile->getUser()->getPerson()->getDocument(),
            area: $profile->getArea(),
            position: $profile->getPosition(),
            email: $email ? $email->getValue() : 'Correo no registrado',
            phone: $phone ? $phone->getValue() : 'Teléfono no registrado',
        );
    }
}

