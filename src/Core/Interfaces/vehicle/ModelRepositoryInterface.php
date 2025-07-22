<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\ModelModel;

interface ModelRepositoryInterface
{
    public function findAllModelByName(string $name): ?ModelModel;
    public function saveModel(ModelModel $modelModel): ModelModel;

    // Métodos CRUD adicionales
    public function findAll(int $page = 1, int $perPage = 15, array $filters = [], string $sortBy = 'name', string $sortOrder = 'ASC'): array;
    public function findById(int $id): ?ModelModel;
    public function create(ModelModel $model): ModelModel;
    public function update(ModelModel $model): ModelModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function existsByNameAndBrand(string $name, int $brandId, ?int $excludeId = null): bool;
    public function findByBrandId(int $brandId): array;
    public function countTotal(array $filters = []): int;
    public function findActiveByBrandId(int $brandId): array;
}