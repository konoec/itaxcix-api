<?php

namespace itaxcix\Core\UseCases\Configuration;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationRequestDTO;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationResponseDTO;

class ConfigurationUpdateUseCase
{
    private ConfigurationRepositoryInterface $repository;

    public function __construct(ConfigurationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ConfigurationRequestDTO $dto): ConfigurationResponseDTO
    {
        $configuration = $this->repository->findById($dto->getId());
        if (!$configuration) {
            throw new InvalidArgumentException("Configuración no encontrada con ID: " . $dto->getId());
        }

        if ($this->repository->existsByKey($dto->getKey(), $dto->getId())) {
            throw new InvalidArgumentException("Ya existe otra configuración con la clave: " . $dto->getKey());
        }

        $configuration->setKey($dto->getKey());
        $configuration->setValue($dto->getValue());
        $configuration->setActive($dto->isActive());

        $updatedConfiguration = $this->repository->update($configuration);

        return ConfigurationResponseDTO::fromModel($updatedConfiguration);
    }
}
