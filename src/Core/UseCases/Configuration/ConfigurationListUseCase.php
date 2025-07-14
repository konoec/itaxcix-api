<?php

namespace itaxcix\Core\UseCases\Configuration;

use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationResponseDTO;

class ConfigurationListUseCase
{
    private ConfigurationRepositoryInterface $repository;

    public function __construct(ConfigurationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ConfigurationPaginationRequestDTO $dto): array
    {
        $result = $this->repository->findAll($dto);

        // Transformar los modelos a DTOs de respuesta
        $items = array_map(
            fn($model) => ConfigurationResponseDTO::fromModel($model)->toArray(),
            $result['items']
        );

        return [
            'items' => $items,
            'meta' => $result['meta']
        ];
    }
}
