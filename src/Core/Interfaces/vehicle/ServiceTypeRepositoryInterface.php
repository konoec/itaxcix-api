<?php

namespace itaxcix\Core\Interfaces\vehicle;


use itaxcix\Core\Domain\vehicle\ServiceTypeModel;

interface ServiceTypeRepositoryInterface
{

    public function findAllServiceTypeByName(string $name): ?ServiceTypeModel;
    public function saveServiceType(ServiceTypeModel $serviceTypeModel): ServiceTypeModel;

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function findById(int $id): ?ServiceTypeModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function countAll(array $filters = []): int;
}
