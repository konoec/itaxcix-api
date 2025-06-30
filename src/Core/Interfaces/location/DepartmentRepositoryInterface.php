<?php

namespace itaxcix\Core\Interfaces\location;

use itaxcix\Core\Domain\location\DepartmentModel;

interface DepartmentRepositoryInterface
{
    // Métodos existentes
    public function findDepartmentByName(string $name): ?DepartmentModel;
    public function saveDepartment(DepartmentModel $departmentModel): DepartmentModel;

    // Métodos CRUD adicionales
    public function findAll(int $page = 1, int $limit = 15, ?string $search = null, ?string $orderBy = 'name', string $orderDirection = 'ASC'): array;
    public function findById(int $id): ?DepartmentModel;
    public function create(DepartmentModel $departmentModel): DepartmentModel;
    public function update(DepartmentModel $departmentModel): DepartmentModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function existsByUbigeo(string $ubigeo, ?int $excludeId = null): bool;
    public function count(?string $search = null): int;
}