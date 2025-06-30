<?php

namespace itaxcix\Core\Handler\Travel;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Domain\location\CoordinatesModel;
use itaxcix\Core\Domain\travel\TravelModel;
use itaxcix\Core\Interfaces\location\CoordinatesRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\UseCases\Travel\RequestNewTravelUseCase;
use itaxcix\Infrastructure\Cache\RedisService;
use itaxcix\Shared\DTO\useCases\Travel\RequestTravelDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class RequestNewTravelUseCaseHandler implements RequestNewTravelUseCase
{
    private CitizenProfileRepositoryInterface $citizenProfileRepository;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private DistrictRepositoryInterface $districtRepository;
    private TravelStatusRepositoryInterface $travelStatusRepository;
    private TravelRepositoryInterface $travelRepository;
    private CoordinatesRepositoryInterface $coordinatesRepository;
    private RedisService $redisService;
    public function __construct(
        CitizenProfileRepositoryInterface $citizenProfileRepository,
        DriverProfileRepositoryInterface $driverProfileRepository,
        DistrictRepositoryInterface $districtRepository,
        TravelStatusRepositoryInterface $travelStatusRepository,
        TravelRepositoryInterface $travelRepository,
        CoordinatesRepositoryInterface $coordinatesRepository,
        RedisService $redisService
    ) {
        $this->citizenProfileRepository = $citizenProfileRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->districtRepository = $districtRepository;
        $this->travelStatusRepository = $travelStatusRepository;
        $this->travelRepository = $travelRepository;
        $this->coordinatesRepository = $coordinatesRepository;
        $this->redisService = $redisService;
    }

    public function execute(RequestTravelDTO $dto): ?TravelResponseDTO
    {
        $travelStatus = $this->travelStatusRepository->findTravelStatusByName('SOLICITADO');
        $citizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($dto->citizenId);
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($dto->driverId);
        $originDistrict = $this->districtRepository->findDistrictByName($dto->originDistrict);
        $destinationDistrict = $this->districtRepository->findDistrictByName($dto->destinationDistrict);

        if (!$citizenProfile || !$driverProfile) {
            throw new InvalidArgumentException('Conductor o o ciudadano no encontrado');
        }

        if (!$originDistrict || !$destinationDistrict) {
            throw new InvalidArgumentException('Distrito de origen o destino no encontrado');
        }

        if (!$travelStatus) {
            throw new InvalidArgumentException('Estado de viaje no encontrado');
        }

        $citizen = $citizenProfile->getUser();
        $driver = $driverProfile->getUser();

        $origin = new CoordinatesModel(
            id: null,
            name: $dto->originAddress,
            district: $originDistrict,
            latitude: (string) $dto->originLatitude,
            longitude: (string) $dto->originLongitude
        );

        $destination = new CoordinatesModel(
            id: null,
            name: $dto->destinationAddress,
            district: $destinationDistrict,
            latitude: (string) $dto->destinationLatitude,
            longitude: (string) $dto->destinationLongitude
        );

        $origin = $this->coordinatesRepository->saveCoordinates($origin);
        $destination = $this->coordinatesRepository->saveCoordinates($destination);

        $travel = new TravelModel(
            id: null,
            citizen: $citizen,
            driver: $driver,
            origin: $origin,
            destination: $destination,
            startDate: null,
            endDate: null,
            creationDate: new DateTime(),
            status: $travelStatus
        );

        $savedTravel = $this->travelRepository->saveTravel($travel);

        // Crear notificación para el conductor
        $notification = [
            'type' => 'trip_request',
            'recipientType' => 'driver',
            'recipientId' => $driver->getId(),
            'data' => [
                'tripId' => $savedTravel->getId(),
                'passengerId' => $citizen->getId(),
                'passengerName' => $citizen->getPerson()->getName() . ' ' . $citizen->getPerson()->getLastName(),
                'origin' => [
                    'lat' => $dto->originLatitude,
                    'lng' => $dto->originLongitude
                ],
                'destination' => [
                    'lat' => $dto->destinationLatitude,
                    'lng' => $dto->destinationLongitude
                ],
                'passengerRating' => $citizenProfile->getAverageRating()
            ],
            'timestamp' => time() // Agregar timestamp para validación TTL
        ];

        // En RequestNewTravelUseCaseHandler.php
        $this->redisService->getClient()->lpush(
            'trip_notifications_queue',
            json_encode($notification)
        );

        return  new TravelResponseDTO(
            message: "Viaje solicitado exitosamente",
            travelId: $savedTravel->getId()
        );
    }
}