<?php

namespace itaxcix\Core\UseCases\IncidentReport;

use itaxcix\Core\Interfaces\incident\IncidentReportRepositoryInterface;
use itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportPaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportResponseDTO;

class IncidentReportUseCase
{
    private IncidentReportRepositoryInterface $repository;

    public function __construct(IncidentReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(IncidentReportRequestDTO $dto): IncidentReportPaginationResponseDTO
    {
        $result = $this->repository->findReport($dto);
        $total = $this->repository->countReport($dto);
        $totalPages = (int) ceil($total / $dto->perPage);
        return new IncidentReportPaginationResponseDTO(
            data: array_map(fn($item) => $item instanceof IncidentReportResponseDTO ? $item : IncidentReportResponseDTO::fromArray($item), $result),
            currentPage: $dto->page,
            perPage: $dto->perPage,
            totalItems: $total,
            totalPages: $totalPages
        );
    }
}

