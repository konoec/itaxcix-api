<?php

namespace itaxcix\Core\UseCases\UserReport;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Shared\DTO\useCases\UserReport\UserReportRequestDTO;
use itaxcix\Shared\DTO\useCases\UserReport\UserReportPaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\UserReport\UserReportResponseDTO;

class UserReportUseCase
{
    private UserRepositoryInterface $repository;
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UserReportRequestDTO $dto): UserReportPaginationResponseDTO
    {
        $result = $this->repository->findReport($dto);
        $total = $this->repository->countReport($dto);
        $totalPages = (int) ceil($total / $dto->perPage);
        return new UserReportPaginationResponseDTO(
            data: array_map(fn($item) => $item instanceof UserReportResponseDTO ? $item : UserReportResponseDTO::fromArray($item), $result),
            currentPage: $dto->page,
            perPage: $dto->perPage,
            totalItems: $total,
            totalPages: $totalPages
        );
    }
}


