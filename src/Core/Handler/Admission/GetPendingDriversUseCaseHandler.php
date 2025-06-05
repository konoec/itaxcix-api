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

        $items = array_map(fn($profile) => new PendingDriverResponseDTO(
            driverId:      $profile->getUser()->getId(),
            fullName:      $profile->getUser()->getPerson()->getName() . ' ' . $profile->getUser()->getPerson()->getLastName(),
            documentValue: $profile->getUser()->getPerson()->getDocument(),
            plateValue:    $this->vehicleUserRepository->findVehicleUserByUserId($profile->getUser()->getId())->getVehicle()->getLicensePlate(),
            contactValue:  $this->userContactRepository->findUserContactByUserId($profile->getUser()->getId())->getValue()
        ), $profiles);

        $lastPage = (int) ceil($total / $perPage);
        $meta     = new PaginationMetaDTO(total: $total, perPage: $perPage, currentPage: $page, lastPage: $lastPage);

        return new PaginationResponseDTO(items: $items, meta: $meta);
    }
}