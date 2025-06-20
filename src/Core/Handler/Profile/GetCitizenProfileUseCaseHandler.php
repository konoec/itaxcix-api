<?php

namespace itaxcix\Core\Handler\Profile;

use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Domain\user\CitizenProfileModel;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\UseCases\Profile\GetCitizenProfileUseCase;
use itaxcix\Shared\DTO\useCases\Profile\CitizenProfileResponseDTO;

class GetCitizenProfileUseCaseHandler implements GetCitizenProfileUseCase
{
    private CitizenProfileRepositoryInterface $citizenProfileRepository;
    private UserContactRepositoryInterface $userContactRepository;


    public function __construct(
        CitizenProfileRepositoryInterface $citizenProfileRepository,
        UserContactRepositoryInterface $userContactRepository
    )
    {
        $this->citizenProfileRepository = $citizenProfileRepository;
        $this->userContactRepository = $userContactRepository;
    }

    public function execute(int $userId): ?CitizenProfileResponseDTO
    {
        $profile = $this->citizenProfileRepository->findCitizenProfileByUserId($userId);
        if (!$profile) {
            return null;
        }

        $email = $this->userContactRepository->findUserContactByUserIdAndContactTypeId($userId, 1);
        $phone = $this->userContactRepository->findUserContactByUserIdAndContactTypeId($userId, 2);

        return new CitizenProfileResponseDTO(
            firstName: $profile->getUser()->getPerson()->getName(),
            lastName: $profile->getUser()->getPerson()->getLastName(),
            documentType: $profile->getUser()->getPerson()->getDocumentType()->getName(),
            document: $profile->getUser()->getPerson()->getDocument(),
            email: $email ? $email->getValue() : 'Correo no registrado',
            phone: $phone ? $phone->getValue() : 'Teléfono no registrado',
            averageRating: $profile->getAverageRating(),
            ratingsCount: $profile->getRatingCount(),
        );
    }
}

