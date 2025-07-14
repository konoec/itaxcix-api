<?php

namespace itaxcix\Core\UseCases\Configuration;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;

class ConfigurationDeleteUseCase
{
    private ConfigurationRepositoryInterface $repository;

    public function __construct(ConfigurationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): array
    {
        // Verificar que la configuración existe
        $configuration = $this->repository->findById($id);
        if (!$configuration) {
            throw new InvalidArgumentException("Configuración no encontrada con ID: " . $id);
        }

        // Verificar que no sea una configuración crítica del sistema
        if ($this->isCriticalConfiguration($configuration->getKey())) {
            throw new InvalidArgumentException("No se puede eliminar la configuración crítica: " . $configuration->getKey());
        }

        // Eliminar (desactivar) la configuración
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            throw new InvalidArgumentException("Error al eliminar la configuración.");
        }

        return [
            'message' => 'Configuración eliminada correctamente.'
        ];
    }

    /**
     * Verifica si una configuración es crítica y no debe ser eliminada
     */
    private function isCriticalConfiguration(string $key): bool
    {
        $criticalKeys = [
            'ITAXCIX_NUMERO_EMERGENCIA'
        ];

        return in_array($key, $criticalKeys);
    }
}
