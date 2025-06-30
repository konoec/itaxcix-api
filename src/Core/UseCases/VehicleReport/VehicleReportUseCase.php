<?php

namespace itaxcix\Core\UseCases\VehicleReport;

use itaxcix\Core\Interfaces\vehicle\VehicleReportRepositoryInterface;
use itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportPaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportResponseDTO;

class VehicleReportUseCase
{
    private VehicleReportRepositoryInterface $repository;

    public function __construct(VehicleReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(VehicleReportRequestDTO $dto): VehicleReportPaginationResponseDTO
    {
        $result = $this->repository->findReport($dto);
        $total = $this->repository->countReport($dto);
        $totalPages = (int) ceil($total / $dto->perPage);
        return new VehicleReportPaginationResponseDTO(
            data: array_map(fn($item) => $item instanceof VehicleReportResponseDTO ? $item : VehicleReportResponseDTO::fromArray($item), $result),
            currentPage: $dto->page,
            perPage: $dto->perPage,
            totalItems: $total,
            totalPages: $totalPages
        );
    }
}

