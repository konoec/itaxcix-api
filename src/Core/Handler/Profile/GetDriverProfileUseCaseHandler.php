<?php

namespace itaxcix\Core\Handler\Profile;

use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Profile\GetDriverProfileUseCase;
use itaxcix\Shared\DTO\useCases\Profile\DriverProfileResponseDTO;

class GetDriverProfileUseCaseHandler implements GetDriverProfileUseCase
{
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private TucProcedureRepositoryInterface $tucProcedureRepository;

    public function __construct(DriverProfileRepositoryInterface $driverProfileRepository,
    UserContactRepositoryInterface $userContactRepository,
    VehicleUserRepositoryInterface $vehicleUserRepository,
    TucProcedureRepositoryInterface $tucProcedureRepository)
    {
        $this->driverProfileRepository = $driverProfileRepository;
        $this->userContactRepository = $userContactRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->tucProcedureRepository = $tucProcedureRepository;
    }

    public function execute(int $userId): ?DriverProfileResponseDTO
    {
        $profile = $this->driverProfileRepository->findDriverProfileByUserId($userId);

        if (!$profile) {
            throw new \InvalidArgumentException('El usuario no tiene un perfil de conductor asociado.');
        }

        $email = $this->userContactRepository->findConfirmedContactByUserAndType($userId, 1);
        $phone = $this->userContactRepository->findConfirmedContactByUserAndType($userId, 2);

        $vehicle = $this->vehicleUserRepository->findVehicleUserByUserId($userId);

        if (!$vehicle) {
            throw new \InvalidArgumentException('El usuario no tiene un vehículo asociado.');
        }

        $tuc = $this->tucProcedureRepository->findTucProcedureByVehicleId($vehicle->getVehicle()->getId());

        return new DriverProfileResponseDTO(
            firstName: $profile->getUser()->getPerson()->getName(),
            lastName: $profile->getUser()->getPerson()->getLastName(),
            documentType: $profile->getUser()->getPerson()->getDocumentType()->getName(),
            document: $profile->getUser()->getPerson()->getDocument(),
            email: $email ? $email->getValue() : 'Correo no registrado',
            phone: $phone ? $phone->getValue() : 'Teléfono no registrado',
            averageRating: $profile->getAverageRating(),
            ratingsCount: $profile->getRatingCount(),
            carPlate: $vehicle->getVehicle()->getLicensePlate(),
            tucStatus: $tuc->getStatus()->getName(),
            tucExpirationDate: $tuc->getExpirationDate()?->format('Y-m-d'),
        );
    }
}

