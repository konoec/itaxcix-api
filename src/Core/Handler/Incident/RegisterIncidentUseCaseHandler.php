<?php

namespace itaxcix\Core\Handler\Incident;

use InvalidArgumentException;
use itaxcix\Core\Domain\incident\IncidentModel;
use itaxcix\Core\Interfaces\incident\IncidentRepositoryInterface;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\UseCases\Incident\RegisterIncidentUseCase;
use itaxcix\Shared\DTO\useCases\Incident\RegisterIncidentRequestDTO;

class RegisterIncidentUseCaseHandler implements RegisterIncidentUseCase
{
    private IncidentRepositoryInterface $incidentRepository;
    private IncidentTypeRepositoryInterface $incidentTypeRepository;
    private UserRepositoryInterface $userRepository;
    private TravelRepositoryInterface $travelRepository;

    public function __construct(
        IncidentRepositoryInterface $incidentRepository,
        IncidentTypeRepositoryInterface $incidentTypeRepository,
        UserRepositoryInterface $userRepository,
        TravelRepositoryInterface $travelRepository
    ) {
        $this->incidentRepository = $incidentRepository;
        $this->incidentTypeRepository = $incidentTypeRepository;
        $this->userRepository = $userRepository;
        $this->travelRepository = $travelRepository;
    }

    public function execute(RegisterIncidentRequestDTO $dto): array
    {
        $incidentType = $this->incidentTypeRepository->findIncidentTypeByName($dto->typeName);
        if (!$incidentType) {
            throw new InvalidArgumentException('El tipo de incidencia no existe.');
        }
        $user = $this->userRepository->findUserById($dto->userId);
        if (!$user) {
            throw new InvalidArgumentException('El usuario no existe.');
        }
        $travel = $this->travelRepository->findTravelById($dto->travelId);
        if (!$travel) {
            throw new InvalidArgumentException('El viaje no existe.');
        }
        // Verificar que el usuario esté relacionado con el viaje (como ciudadano o conductor)
        if ($travel->getCitizen()->getId() !== $user->getId() && $travel->getDriver()->getId() !== $user->getId()) {
            throw new InvalidArgumentException('El usuario no está relacionado con el viaje.');
        }
        if ($travel->getStatus()->getName() !== 'CANCELADO') {
            throw new InvalidArgumentException('El viaje debe estar cancelado para registrar un incidente.');
        }
        $incident = new IncidentModel(
            id: null,
            user: $user,
            travel: $travel,
            type: $incidentType,
            comment: $dto->comment,
            active: true
        );
        $incident = $this->incidentRepository->saveIncident($incident);
        return [
            'incidentId' => $incident->getId(),
            'message' => 'Incidente registrado correctamente.'
        ];
    }
}
