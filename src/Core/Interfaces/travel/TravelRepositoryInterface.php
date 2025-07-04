<?php

namespace itaxcix\Core\Interfaces\travel;

use itaxcix\Core\Domain\travel\TravelModel;

interface TravelRepositoryInterface
{
    public function saveTravel(TravelModel $travelModel): TravelModel;
    public function findTravelById(int $id): ?TravelModel;
    public function findTravelsByUserId(int $userId, int $offset, int $perPage): array;
    public function countTravelsByUserId(int $userId): int;
    public function findReport(\itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO $dto): array;
    public function countReport(\itaxcix\Shared\DTO\useCases\TravelReport\TravelReportRequestDTO $dto): int;
}