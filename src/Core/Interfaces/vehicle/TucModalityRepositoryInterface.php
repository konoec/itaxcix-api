<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\TucModalityModel;

interface TucModalityRepositoryInterface
{
    public function findAllTucModalityByName(string $name): ?TucModalityModel;
    public function saveTucModality(TucModalityModel $brandModel): TucModalityModel;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function findById(int $id): ?TucModalityModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function countAll(array $filters = []): int;
}