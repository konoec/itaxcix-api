<?php

namespace itaxcix\Core\UseCases\InfractionReport;

use itaxcix\Core\Interfaces\infraction\InfractionRepositoryInterface;
use itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportPaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportResponseDTO;

class InfractionReportUseCase
{
    private InfractionRepositoryInterface $repository;

    public function __construct(InfractionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(InfractionReportRequestDTO $dto): InfractionReportPaginationResponseDTO
    {
        $result = $this->repository->findReport($dto);
        $total = $this->repository->countReport($dto);
        $totalPages = (int) ceil($total / $dto->perPage);
        return new InfractionReportPaginationResponseDTO(
            data: array_map(fn($item) => $item instanceof InfractionReportResponseDTO ? $item : InfractionReportResponseDTO::fromArray($item), $result),
            currentPage: $dto->page,
            perPage: $dto->perPage,
            totalItems: $total,
            totalPages: $totalPages
        );
    }
}

