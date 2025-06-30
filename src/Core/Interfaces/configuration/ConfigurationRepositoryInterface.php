<?php

namespace itaxcix\Core\Interfaces\configuration;

use itaxcix\Core\Domain\configuration\ConfigurationModel;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationPaginationRequestDTO;

interface ConfigurationRepositoryInterface
{
    public function findAll(ConfigurationPaginationRequestDTO $dto): array;
    public function findById(int $id): ?ConfigurationModel;
    public function findByKey(string $key): ?ConfigurationModel;
    public function create(ConfigurationModel $configuration): ConfigurationModel;
    public function update(ConfigurationModel $configuration): ConfigurationModel;
    public function delete(int $id): bool;
    public function existsByKey(string $key, ?int $excludeId = null): bool;
}
