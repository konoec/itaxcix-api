<?php

namespace itaxcix\Core\Interfaces\Driver;

use itaxcix\Core\Domain\user\DriverStatusModel;

interface DriverStatusRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?DriverStatusModel;

    public function create(DriverStatusModel $driverStatus): DriverStatusModel;

    public function update(DriverStatusModel $driverStatus): DriverStatusModel;

    public function delete(int $id): bool;

    public function existsByName(string $name, ?int $excludeId = null): bool;

    public function findWithFilters(
        ?string $search = null,
        ?string $name = null,
        ?bool $active = null,
        string $sortBy = 'name',
        string $sortDirection = 'asc',
        int $page = 1,
        int $perPage = 15,
        bool $onlyActive = false
    ): array;

    public function countWithFilters(
        ?string $search = null,
        ?string $name = null,
        ?bool $active = null,
        bool $onlyActive = false
    ): int;
}
