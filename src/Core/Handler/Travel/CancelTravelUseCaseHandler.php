<?php

namespace itaxcix\Core\Handler\Travel;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Core\UseCases\Travel\CancelTravelUseCase;
use itaxcix\Infrastructure\Cache\RedisService;
use itaxcix\Shared\DTO\useCases\Travel\TravelIdDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class CancelTravelUseCaseHandler implements CancelTravelUseCase
{
    private TravelRepositoryInterface $travelRepository;
    private TravelStatusRepositoryInterface $travelStatusRepository;
    private RedisService $redisService;

    public function __construct(
        TravelRepositoryInterface $travelRepository,
        TravelStatusRepositoryInterface $travelStatusRepository,
        RedisService $redisService
    ) {
        $this->travelRepository = $travelRepository;
        $this->travelStatusRepository = $travelStatusRepository;
        $this->redisService = $redisService;
    }

    public function execute(TravelIdDTO $dto): ?TravelResponseDTO
    {
        $travel = $this->travelRepository->findTravelById($dto->travelId);
        $cancelledStatus = $this->travelStatusRepository->findTravelStatusByName('CANCELADO');

        if (!$travel) {
            throw new InvalidArgumentException('Viaje no encontrado');
        }

        if (!$cancelledStatus) {
            throw new InvalidArgumentException('Estado de viaje "CANCELADO" no encontrado');
        }

        if ($travel->getStatus()->getName() === 'CANCELADO') {
            throw new InvalidArgumentException('El viaje ya está cancelado');
        }

        if ($travel->getStatus()->getName() === 'RECHAZADO') {
            throw new InvalidArgumentException('El viaje fue rechazado');
        }

        if ($travel->getStatus()->getName() === 'SOLICITADO') {
            throw new InvalidArgumentException('El viaje fue solicitado y no puede ser cancelado');
        }

        if ($travel->getStatus()->getName() === 'FINALIZADO') {
            throw new InvalidArgumentException('El viaje está finalizado y no puede ser cancelado');
        }

        $travel->setStatus($cancelledStatus);
        $updatedTravel = $this->travelRepository->saveTravel($travel);

        // Notificar al conductor
        $driverNotification = [
            'type' => 'trip_status_update',
            'recipientType' => 'driver',
            'recipientId' => (string) $travel->getDriver()->getId(),
            'data' => [
                'tripId' => $updatedTravel->getId(),
                'status' => 'canceled',
                'driverId' => $travel->getDriver()->getId()
            ]
        ];

        // Notificar al ciudadano
        $citizenNotification = [
            'type' => 'trip_status_update',
            'recipientType' => 'citizen',
            'recipientId' => (string) $travel->getCitizen()->getId(),
            'data' => [
                'tripId' => $updatedTravel->getId(),
                'status' => 'canceled',
                'driverId' => $travel->getDriver()->getId()
            ]
        ];

        // Enviar notificaciones a través de Redis
        $this->redisService->getClient()->lpush(
            'trip_notifications_queue',
            (array)json_encode($driverNotification)
        );

        $this->redisService->getClient()->lpush(
            'trip_notifications_queue',
            (array)json_encode($citizenNotification)
        );

        return new TravelResponseDTO(
            message: 'Viaje cancelado exitosamente',
            travelId: $updatedTravel->getId()
        );
    }
}