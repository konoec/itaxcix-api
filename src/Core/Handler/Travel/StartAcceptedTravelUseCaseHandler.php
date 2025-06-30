<?php

namespace itaxcix\Core\Handler\Travel;

use DateTimeImmutable;
use InvalidArgumentException;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Core\UseCases\Travel\StartAcceptedTravelUseCase;
use itaxcix\Infrastructure\Cache\RedisService;
use itaxcix\Shared\DTO\useCases\Travel\TravelIdDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class StartAcceptedTravelUseCaseHandler implements StartAcceptedTravelUseCase
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
        $travelStatus = $this->travelStatusRepository->findTravelStatusByName('INICIADO');

        if (!$travel) {
            throw new InvalidArgumentException('Viaje no encontrado');
        }
        if (!$travelStatus) {
            throw new InvalidArgumentException('Estado de viaje no encontrado');
        }
        if ($travel->getStatus()->getName() !== 'ACEPTADO') {
            throw new InvalidArgumentException('El viaje no está en estado ACEPTADO');
        }

        $travel->setStatus($travelStatus);
        $travel->setStartDate(new \DateTime());
        $updatedTravel = $this->travelRepository->saveTravel($travel);

        // Crear notificación según el schema TripStatusUpdatePayload
        $notification = [
            'type' => 'trip_status_update',
            'recipientType' => 'citizen',
            'recipientId' => $travel->getCitizen()->getId(),
            'data' => [
                'tripId' => $updatedTravel->getId(),
                'status' => 'started',
                'driverId' => $travel->getDriver()->getId()
            ],
            'timestamp' => time() // Agregar timestamp para validación TTL
        ];

        // Enviar a la cola de Redis
        $this->redisService->getClient()->lpush(
            'trip_notifications_queue',
            (array)json_encode($notification)
        );

        return new TravelResponseDTO(
            message: 'El viaje ha sido iniciado',
            travelId: $updatedTravel->getId()
        );
    }
}