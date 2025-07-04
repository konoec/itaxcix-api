<?php

namespace itaxcix\Core\UseCases\RatingReport;

use itaxcix\Core\Interfaces\rating\RatingRepositoryInterface;
use itaxcix\Shared\DTO\useCases\RatingReport\RatingReportRequestDTO;
use itaxcix\Shared\DTO\useCases\RatingReport\RatingReportPaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\RatingReport\RatingReportResponseDTO;

class RatingReportUseCase
{
    private RatingRepositoryInterface $repository;

    public function __construct(RatingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(RatingReportRequestDTO $dto): RatingReportPaginationResponseDTO
    {
        $result = $this->repository->findReport($dto);
        $total = $this->repository->countReport($dto);
        $totalPages = (int) ceil($total / $dto->perPage);
        return new RatingReportPaginationResponseDTO(
            data: array_map(fn($item) => $item instanceof RatingReportResponseDTO ? $item : RatingReportResponseDTO::fromArray($item), $result),
            currentPage: $dto->page,
            perPage: $dto->perPage,
            totalItems: $total,
            totalPages: $totalPages
        );
    }
}

