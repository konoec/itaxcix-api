<?php

namespace itaxcix\Core\Handler\Admission;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Admission\GetDriverDetailsUseCase;
use itaxcix\Shared\DTO\useCases\Admission\PendingDriverDetailsResponseDTO;

class GetDriverDetailsUseCaseHandler implements GetDriverDetailsUseCase
{
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private TucProcedureRepositoryInterface $tucProcedureRepository;

    public function __construct(
        DriverProfileRepositoryInterface $driverProfileRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository,
        UserContactRepositoryInterface $userContactRepository,
        TucProcedureRepositoryInterface $tucProcedureRepository
    ) {
        $this->driverProfileRepository = $driverProfileRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->userContactRepository = $userContactRepository;
        $this->tucProcedureRepository = $tucProcedureRepository;
    }

    public function execute(int $driverId): ?PendingDriverDetailsResponseDTO
    {
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($driverId);

        if (!$driverProfile) {
            throw new InvalidArgumentException('El perfil del conductor no existe.');
        }

        if ($driverProfile->getStatus()->getName() !== 'PENDIENTE') {
            throw new InvalidArgumentException('El conductor no está en estado pendiente.');
        }

        $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($driverProfile->getUser()->getId());
        $contactUser = $this->userContactRepository->findUserContactByUserId($driverProfile->getUser()->getId());
        $tuc = $this->tucProcedureRepository->findTucProcedureByVehicleId($vehicleUser->getVehicle()->getId());

        return new PendingDriverDetailsResponseDTO(
            driverId: $driverProfile->getId(),
            fullName: $driverProfile->getUser()->getPerson()->getName() . ' ' . $driverProfile->getUser()->getPerson()->getLastName(),
            documentValue: $driverProfile->getUser()->getPerson()->getDocument(),
            plateValue: $vehicleUser->getVehicle()->getLicensePlate(),
            contactValue: $contactUser->getValue(),
            rucCompany: $tuc->getCompany()->getRuc(),
            tucType: $tuc->getType()->getName(),
            tucModality: $tuc->getModality()->getName(),
            tucIssueDate: $tuc->getIssueDate()->format('Y-m-d'),
            tucExpirationDate: $tuc->getExpirationDate()->format('Y-m-d'),
            tucStatus: $tuc->getStatus()->getName(),
        );
    }
}