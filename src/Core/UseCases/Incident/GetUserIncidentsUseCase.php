<?php

namespace itaxcix\Core\UseCases\Incident;

use itaxcix\Core\Interfaces\incident\IncidentRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Incident\GetUserIncidentsRequestDTO;

class GetUserIncidentsUseCase
{
    private IncidentRepositoryInterface $incidentRepository;

    public function __construct(IncidentRepositoryInterface $incidentRepository)
    {
        $this->incidentRepository = $incidentRepository;
    }

    public function execute(GetUserIncidentsRequestDTO $dto): array
    {
        $incidents = $this->incidentRepository->findByUser($dto);
        $total = $this->incidentRepository->countByUser($dto);

        $totalPages = ceil($total / $dto->perPage);

        return [
            'incidents' => $incidents,
            'pagination' => [
                'currentPage' => $dto->page,
                'perPage' => $dto->perPage,
                'total' => $total,
                'totalPages' => $totalPages,
                'hasNextPage' => $dto->page < $totalPages,
                'hasPreviousPage' => $dto->page > 1
            ]
        ];
    }
}
