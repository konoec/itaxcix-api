<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\ProcedureTypeModel;

interface ProcedureTypeRepositoryInterface
{
    public function findAllProcedureTypeByName(string $name): ?ProcedureTypeModel;
    public function saveProcedureType(ProcedureTypeModel $procedureTypeModel): ProcedureTypeModel;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function findById(int $id): ?ProcedureTypeModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function countAll(array $filters = []): int;
}