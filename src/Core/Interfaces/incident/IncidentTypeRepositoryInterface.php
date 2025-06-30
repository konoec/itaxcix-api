<?php

namespace itaxcix\Core\Interfaces\incident;

use itaxcix\Core\Domain\incident\IncidentTypeModel;
use itaxcix\Infrastructure\Database\Entity\incident\IncidentTypeEntity;

interface IncidentTypeRepositoryInterface {
    public function findIncidentTypeByName(string $name): ?IncidentTypeModel;

    // Métodos para CRUD y paginación similares a ServiceType
    public function saveIncidentType(IncidentTypeModel $incidentTypeModel): IncidentTypeModel;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function findById(int $id): ?IncidentTypeModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function countAll(array $filters = []): int;
}
