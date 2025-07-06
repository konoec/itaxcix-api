<?php

namespace itaxcix\Core\Handler\Admission;

use Exception;
use InvalidArgumentException;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Admission\GetPendingDriversUseCase;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
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

    /**
     * @throws Exception
     */
    public function execute(int $page, int $perPage): PaginationResponseDTO
    {
        $status = $this->driverStatusRepository->findDriverStatusByName('PENDIENTE');
        if (!$status) {
            throw new InvalidArgumentException('Estado PENDIENTE no existe');
        }

        $total   = $this->driverProfileRepository->countDriversProfilesByStatusId($status->getId());
        $offset  = ($page - 1) * $perPage;
        $profiles = $this->driverProfileRepository->findDriversProfilesByStatusId($status->getId(), $offset, $perPage);

        $items = array_values(array_filter(array_map(function($profile) {
            // Verificar que $profile y $profile->getUser() no sean null
            if (!$profile || !$profile->getUser()) {
                error_log('Profile o User es null');
                return null;
            }

            $user = $profile->getUser();
            $userId = $user->getId();

            // Verificar que userId no sea null
            if (!$userId) {
                error_log('UserId es null');
                return null;
            }

            $contact = $this->userContactRepository->findUserContactByUserId($userId);
            if (!$contact || !$contact->isConfirmed()) {
                return null;
            }

            $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($userId);
            error_log('VehicleUser: ' . ($vehicleUser ? 'existe' : 'es null') . ' para userId: ' . $userId);

            $plateValue = null;
            if ($vehicleUser !== null) {
                $vehicle = $vehicleUser->getVehicle();
                error_log('Vehicle: ' . ($vehicle ? 'existe' : 'es null'));
                if ($vehicle !== null) {
                    $plateValue = $vehicle->getLicensePlate();
                }
            }

            // Verificar que Person no sea null
            $person = $user->getPerson();
            if (!$person) {
                error_log('Person es null para userId: ' . $userId);
                return null;
            }

            return new PendingDriverResponseDTO(
                driverId:      $userId,
                fullName:      $person->getName() . ' ' . $person->getLastName(),
                documentValue: $person->getDocument(),
                plateValue:    $plateValue,
                contactValue:  $contact->getValue()
            );
        }, $profiles)));

        $lastPage = (int) ceil($total / $perPage);
        $meta     = new PaginationMetaDTO(total: $total, perPage: $perPage, currentPage: $page, lastPage: $lastPage);

        return new PaginationResponseDTO(items: $items, meta: $meta);
    }
}