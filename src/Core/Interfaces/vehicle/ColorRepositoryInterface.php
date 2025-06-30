<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\ColorModel;

interface ColorRepositoryInterface
{
    public function findAllColorByName(string $name): ?ColorModel;
    public function saveColor(ColorModel $colorModel): ColorModel;

    // Métodos CRUD adicionales
    public function findAll(): array;
    public function findById(int $id): ?ColorModel;
    public function create(ColorModel $colorModel): ColorModel;
    public function update(ColorModel $colorModel): ColorModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function findWithPagination(int $page, int $perPage, array $filters = [], string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function getTotalCount(array $filters = []): int;
}