<?php

namespace itaxcix\Core\Handler\Travel;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Core\UseCases\Travel\CompleteTravelUseCase;
use itaxcix\Shared\DTO\useCases\Travel\TravelIdDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;
use itaxcix\Infrastructure\Cache\RedisService;

class CompleteTravelUseCaseHandler implements CompleteTravelUseCase
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
        $completedStatus = $this->travelStatusRepository->findTravelStatusByName('FINALIZADO');

        if (!$travel) {
            throw new InvalidArgumentException('Viaje no encontrado');
        }

        if (!$completedStatus) {
            throw new InvalidArgumentException('Estado de viaje "FINALIZADO" no encontrado');
        }

        $travel->setStatus($completedStatus);
        $savedTravel = $this->travelRepository->saveTravel($travel);

        // Enviar notificación a través de Redis
        $notification = [
            'type' => 'trip_status_update',
            'recipientType' => 'citizen',
            'recipientId' => (string) $travel->getCitizen()->getId(),
            'data' => [
                'tripId' => $savedTravel->getId(),
                'status' => 'completed',
                'driverId' => $travel->getDriver()->getId()
            ]
        ];

        // Publicar en la cola de Redis
        $this->redisService->getClient()->lpush(
            'trip_notifications_queue',
            (array)json_encode($notification)
        );

        // Notificar también al conductor
        $driverNotification = array_merge($notification, [
            'recipientType' => 'driver',
            'recipientId' => (string) $travel->getDriver()->getId()
        ]);

        $this->redisService->getClient()->lpush(
            'trip_notifications_queue',
            (array)json_encode($driverNotification)
        );

        return new TravelResponseDTO(
            message: 'Viaje finalizado exitosamente',
            travelId: $travel->getId()
        );
    }
}