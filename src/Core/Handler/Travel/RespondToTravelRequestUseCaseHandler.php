<?php

namespace itaxcix\Core\Handler\Travel;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Core\UseCases\Travel\RespondToTravelRequestUseCase;
use itaxcix\Infrastructure\Cache\RedisService;
use itaxcix\Shared\DTO\useCases\Travel\RespondToRequestDTO;
use itaxcix\Shared\DTO\useCases\Travel\TravelResponseDTO;

class RespondToTravelRequestUseCaseHandler implements RespondToTravelRequestUseCase
{
    private TravelRepositoryInterface $travelRepository;
    private TravelStatusRepositoryInterface $travelStatusRepository;
    private RedisService $redisService;

    public function __construct(
        TravelRepositoryInterface $travelRepository,
        TravelStatusRepositoryInterface $travelStatusRepository,
        RedisService $redisService
    )
    {
        $this->travelRepository = $travelRepository;
        $this->travelStatusRepository = $travelStatusRepository;
        $this->redisService = $redisService;
    }

    public function execute(RespondToRequestDTO $dto): ?TravelResponseDTO
    {
        $travel = $this->travelRepository->findTravelById($dto->travelId);
        $travelAcceptedStatus = $this->travelStatusRepository->findTravelStatusByName('ACEPTADO');
        $travelRejectedStatus = $this->travelStatusRepository->findTravelStatusByName('RECHAZADO');

        if (!$travel) {
            throw new InvalidArgumentException('Viaje no encontrado');
        }

        if (!$travelAcceptedStatus || !$travelRejectedStatus) {
            throw new InvalidArgumentException('Estado de viaje no encontrado');
        }

        if ($travel->getStatus()->getName() !== 'SOLICITADO') {
            throw new InvalidArgumentException('El viaje no está en estado SOLICITADO');
        }

        if ($dto->accepted) {
            $travel->setStatus($travelAcceptedStatus);
        } else {
            $travel->setStatus($travelRejectedStatus);
        }

        $updatedTravel = $this->travelRepository->saveTravel($travel);

        // Crear notificación para el ciudadano según el schema TripResponsePayload
        $notification = [
            'type' => 'trip_response',
            'recipientType' => 'citizen',
            'recipientId' => (string) $travel->getCitizen()->getId(),
            'data' => [
                'tripId' => $updatedTravel->getId(),
                'accepted' => $dto->accepted,
                'driverId' => $travel->getDriver()->getId(),
                'driverName' => $travel->getDriver()->getPerson()->getName() . ' ' . $travel->getDriver()->getPerson()->getLastName(),
                'estimatedArrival' => $dto->estimatedArrival ?? 0
            ]
        ];

        // Enviar a la cola de Redis
        $this->redisService->getClient()->lpush(
            'trip_notifications_queue',
            (array)json_encode($notification)
        );

        return new TravelResponseDTO(
            message: 'Respuesta al viaje procesada correctamente',
            travelId: $updatedTravel->getId()
        );
    }
}