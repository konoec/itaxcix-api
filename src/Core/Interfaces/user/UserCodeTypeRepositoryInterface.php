<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserCodeTypeModel;

interface UserCodeTypeRepositoryInterface
{
    public function findUserCodeTypeByName(string $name): ?UserCodeTypeModel;
    public function findUserCodeTypeById(int $id): ?UserCodeTypeModel;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'ASC'): array;
    public function saveUserCodeType(UserCodeTypeModel $userCodeTypeModel): UserCodeTypeModel;
    public function delete(int $id): bool;
    public function countAll(array $filters = []): int;
    public function existsByName(string $name, ?int $excludeId = null): bool;
}
