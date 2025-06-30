<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\TucStatusModel;

interface TucStatusRepositoryInterface
{
    public function findAllTucStatusByName(string $name): ?TucStatusModel;
    public function saveTucStatus(TucStatusModel $tucStatusModel): TucStatusModel;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function findById(int $id): ?TucStatusModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function countAll(array $filters = []): int;
}