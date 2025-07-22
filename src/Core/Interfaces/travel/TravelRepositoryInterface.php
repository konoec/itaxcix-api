<?php

namespace itaxcix\Core\Interfaces\travel;

use itaxcix\Core\Domain\travel\TravelModel;
use itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO;

interface TravelRepositoryInterface
{
    public function saveTravel(TravelModel $travelModel): TravelModel;

    public function findTravelById(int $id): ?TravelModel;

    public function findTravelsByUserId(int $userId, int $offset, int $perPage): array;

    public function countTravelsByUserId(int $userId): int;

    public function findReport(TravelReportRequestDTO $dto): array;

    public function countReport(TravelReportRequestDTO $dto): int;
    public function findActivesByTravelStatusId(int $travelStatusId): array;
}