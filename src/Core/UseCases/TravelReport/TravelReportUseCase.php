<?php

namespace itaxcix\Core\UseCases\TravelReport;

use itaxcix\Core\Interfaces\travel\TravelReportRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO;
use itaxcix\Shared\DTO\useCases\TravelReport\TravelReportPaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\TravelReport\TravelReportResponseDTO;

class TravelReportUseCase
{
    private TravelReportRepositoryInterface $repository;

    public function __construct(TravelReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(TravelReportRequestDTO $dto): TravelReportPaginationResponseDTO
    {
        $result = $this->repository->findReport($dto);
        $total = $this->repository->countReport($dto);
        $totalPages = (int) ceil($total / $dto->perPage);
        return new TravelReportPaginationResponseDTO(
            data: array_map(fn($item) => $item instanceof TravelReportResponseDTO ? $item : TravelReportResponseDTO::fromArray($item), $result),
            currentPage: $dto->page,
            perPage: $dto->perPage,
            totalItems: $total,
            totalPages: $totalPages
        );
    }
}

