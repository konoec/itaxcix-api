<?php

namespace itaxcix\Core\UseCases\Configuration;

use InvalidArgumentException;
use itaxcix\Core\Domain\configuration\ConfigurationModel;
use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationRequestDTO;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationResponseDTO;

class ConfigurationCreateUseCase
{
    private ConfigurationRepositoryInterface $repository;

    public function __construct(ConfigurationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ConfigurationRequestDTO $dto): array
    {
        // Verificar que la clave no exista
        if ($this->repository->existsByKey($dto->getKey())) {
            throw new InvalidArgumentException("Ya existe una configuración con la clave: " . $dto->getKey());
        }

        // Crear el modelo
        $configuration = new ConfigurationModel(
            id: null,
            key: $dto->getKey(),
            value: $dto->getValue(),
            active: $dto->isActive()
        );

        // Guardar en la base de datos
        $savedConfiguration = $this->repository->create($configuration);

        // Retornar respuesta
        return [
            'configuration' => ConfigurationResponseDTO::fromModel($savedConfiguration),
            'message' => 'Configuración creada correctamente.'
        ];
    }
}
