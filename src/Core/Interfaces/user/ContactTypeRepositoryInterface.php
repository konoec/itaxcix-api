<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\ContactTypeModel;

interface ContactTypeRepositoryInterface
{
    /**
     * @return ContactTypeModel[]
     */
    public function findAll(): array;

    public function findById(int $id): ?ContactTypeModel;

    public function create(ContactTypeModel $contactType): ContactTypeModel;

    public function update(ContactTypeModel $contactType): ContactTypeModel;

    public function delete(int $id): bool;

    public function existsByName(string $name, ?int $excludeId = null): bool;

    /**
     * @param array $filters
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array{data: ContactTypeModel[], total: int}
     */
    public function findWithFilters(array $filters = [], array $orderBy = [], int $limit = 15, int $offset = 0): array;
}
