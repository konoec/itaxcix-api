<?php

namespace itaxcix\Core\Interfaces\travel;

use itaxcix\Core\Domain\travel\TravelStatusModel;

interface TravelStatusRepositoryInterface
{
    public function findTravelStatusByName(string $name): ?TravelStatusModel;
    public function saveTravelStatus(TravelStatusModel $travelStatusModel): TravelStatusModel;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function findById(int $id): ?TravelStatusModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function countAll(array $filters = []): int;
}