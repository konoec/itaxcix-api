<?php

namespace itaxcix\Core\UseCases\DriverStatus;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\Driver\DriverStatusRepositoryInterface;

class DriverStatusDeleteUseCase
{
    private DriverStatusRepositoryInterface $repository;

    public function __construct(DriverStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): array
    {
        // Validar que el registro existe
        $existingModel = $this->repository->findById($id);
        if (!$existingModel) {
            throw new InvalidArgumentException("No se encontró el estado de conductor con ID {$id}.");
        }

        // Realizar eliminación lógica (soft delete)
        $result = $this->repository->delete($id);

        if (!$result) {
            throw new InvalidArgumentException("No se pudo eliminar el estado de conductor.");
        }

        return [
            'message' => 'Estado de conductor eliminado correctamente.'
        ];
    }
}
