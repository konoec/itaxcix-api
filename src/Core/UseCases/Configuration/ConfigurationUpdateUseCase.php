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

    public function execute(ConfigurationRequestDTO $dto): array
    {
        // Verificar que la configuración existe
        $configuration = $this->repository->findById($dto->getId());
        if (!$configuration) {
            throw new InvalidArgumentException("Configuración no encontrada con ID: " . $dto->getId());
        }

        // Verificar que la clave no esté en uso por otra configuración
        if ($this->repository->existsByKey($dto->getKey(), $dto->getId())) {
            throw new InvalidArgumentException("Ya existe otra configuración con la clave: " . $dto->getKey());
        }

        // Actualizar los datos
        $configuration->setKey($dto->getKey());
        $configuration->setValue($dto->getValue());
        $configuration->setActive($dto->isActive());

        // Guardar los cambios
        $updatedConfiguration = $this->repository->update($configuration);

        // Retornar respuesta
        return [
            'configuration' => ConfigurationResponseDTO::fromModel($updatedConfiguration),
            'message' => 'Configuración actualizada correctamente.'
        ];
    }
}
