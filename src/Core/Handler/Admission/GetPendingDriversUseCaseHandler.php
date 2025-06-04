<?php

namespace itaxcix\Core\Handler\Admission;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Admission\GetPendingDriversUseCase;
use itaxcix\Shared\DTO\useCases\Admission\PendingDriverResponseDTO;

class GetPendingDriversUseCaseHandler implements GetPendingDriversUseCase
{
    public DriverProfileRepositoryInterface $driverProfileRepository;
    public DriverStatusRepositoryInterface $driverStatusRepository;
    public VehicleUserRepositoryInterface $vehicleUserRepository;
    public UserContactRepositoryInterface $userContactRepository;

    public function __construct(
        DriverProfileRepositoryInterface $driverProfileRepository,
        DriverStatusRepositoryInterface $driverStatusRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository,
        UserContactRepositoryInterface $userContactRepository,
    ) {
        $this->driverProfileRepository = $driverProfileRepository;
        $this->driverStatusRepository = $driverStatusRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->userContactRepository = $userContactRepository;
    }

    public function execute(): ?array
    {
        $driverStatus = $this->driverStatusRepository->findDriverStatusByName('PENDIENTE');

        if ($driverStatus){
            throw new InvalidArgumentException('No se encontró el estado de conductor PENDIENTE');
        }

        $driversProfiles = $this->driverProfileRepository->findDriversProfilesByStatusId($driverStatus->getId());

        if (empty($driversProfiles)) {
            throw new ('No se encontraron conductores pendientes.');
        }

        $response = [];
        foreach ($driversProfiles as $profile) {
            if (!$profile instanceof DriverProfileModel) {
                throw new InvalidArgumentException('El perfil del conductor no es válido.');
            }

            $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($profile->getUser()->getId());
            $contactUser = $this->userContactRepository->findUserContactByUserId($profile->getUser()->getId());

            $response[] = new PendingDriverResponseDTO(
                driverId: $profile->getId(),
                fullName: $profile->getUser()->getPerson()->getName() . ' ' . $profile->getUser()->getPerson()->getLastName(),
                documentValue: $profile->getUser()->getPerson()->getDocument(),
                plateValue: $vehicleUser->getVehicle()->getLicensePlate(),
                contactValue: $contactUser->getValue()
            );
        }

        return $response;
    }
}