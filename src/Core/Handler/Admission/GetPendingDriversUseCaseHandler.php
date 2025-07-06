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
            // Información de depuración que se incluirá en la respuesta
            $debugInfo = [];

            if (!$profile) {
                $debugInfo[] = 'Profile es null';
                return ['debug' => $debugInfo]; // ESTO ESTÁ BIEN
            }

            $user = $profile->getUser();
            if (!$user) {
                $debugInfo[] = 'User es null';
                return ['debug' => $debugInfo]; // ESTO ESTÁ BIEN
            }

            $userId = $user->getId();
            if (!$userId) {
                $debugInfo[] = 'UserId es null';
                return ['debug' => $debugInfo]; // ESTO ESTÁ BIEN
            }

            $debugInfo[] = "UserId: $userId";

            $contact = $this->userContactRepository->findUserContactByUserId($userId);
            if (!$contact || !$contact->isConfirmed()) {
                return null; // CAMBIA ESTO - debe retornar null, no debug
            }

            $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($userId);

            $plateValue = null;
            if ($vehicleUser !== null) {
                $vehicle = $vehicleUser->getVehicle();
                if ($vehicle !== null) {
                    $plateValue = $vehicle->getLicensePlate();
                }
            }

            $person = $user->getPerson();
            if (!$person) {
                $debugInfo[] = 'Person es null';
                return ['debug' => $debugInfo];
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