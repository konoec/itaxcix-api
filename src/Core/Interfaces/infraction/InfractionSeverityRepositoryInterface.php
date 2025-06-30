<?php

namespace itaxcix\Core\Interfaces\infraction;

use itaxcix\Core\Domain\infraction\InfractionSeverityModel;

interface InfractionSeverityRepositoryInterface {
    public function saveInfractionSeverity(InfractionSeverityModel $infractionSeverityModel): InfractionSeverityModel;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function findById(int $id): ?InfractionSeverityModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function countAll(array $filters = []): int;
}

