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

        $items = [];
        foreach ($profiles as $profile) {
            if (!$profile || !$profile->getUser()) {
                continue;
            }

            $user = $profile->getUser();
            $userId = $user->getId();

            if (!$userId) {
                continue;
            }

            $contact = $this->userContactRepository->findUserContactByUserId($userId);
            if (!$contact || !$contact->isConfirmed()) {
                continue;
            }

            $person = $user->getPerson();
            if (!$person) {
                continue;
            }

            // Obtener vehículo activo del usuario con validación completa
            $plateValue = 'Sin vehículo asignado';

            try {
                $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($userId);

                // Validación robusta para evitar null pointer exceptions
                if ($vehicleUser !== null &&
                    method_exists($vehicleUser, 'isActive') &&
                    $vehicleUser->isActive() &&
                    method_exists($vehicleUser, 'getVehicle') &&
                    $vehicleUser->getVehicle() !== null &&
                    method_exists($vehicleUser->getVehicle(), 'getLicensePlate') &&
                    !empty($vehicleUser->getVehicle()->getLicensePlate())) {
                    $plateValue = $vehicleUser->getVehicle()->getLicensePlate();
                }
            } catch (\Exception $e) {
                // Log del error para debugging si es necesario
                error_log("Error obteniendo vehículo para usuario {$userId}: " . $e->getMessage());
                // Mantener el valor por defecto
                $plateValue = 'Sin vehículo asignado';
            } catch (\Error $e) {
                // Capturar también errores fatales como null pointer
                error_log("Error fatal obteniendo vehículo para usuario {$userId}: " . $e->getMessage());
                $plateValue = 'Sin vehículo asignado';
            }

            $items[] = new PendingDriverResponseDTO(
                driverId:      $userId,
                fullName:      $person->getName() . ' ' . $person->getLastName(),
                documentValue: $person->getDocument(),
                plateValue:    $plateValue,
                contactValue:  $contact->getValue()
            );
        }

        $lastPage = (int) ceil($total / $perPage);
        $meta     = new PaginationMetaDTO(total: $total, perPage: $perPage, currentPage: $page, lastPage: $lastPage);

        return new PaginationResponseDTO(items: $items, meta: $meta);
    }
}