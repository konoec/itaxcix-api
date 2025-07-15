<?php

namespace itaxcix\Core\UseCases\DriverStatus;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\DriverStatusModel;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusResponseDTO;

class DriverStatusUpdateUseCase
{
    private DriverStatusRepositoryInterface $repository;

    public function __construct(DriverStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DriverStatusRequestDTO $request): DriverStatusResponseDTO
    {
        // Validar que el registro existe
        $existingModel = $this->repository->findById($request->id);
        if (!$existingModel) {
            throw new InvalidArgumentException("No se encontró el estado de conductor con ID {$request->id}.");
        }

        // Validar que el nombre no exista (excepto el actual)
        if ($this->repository->existsByName($request->name, $request->id)) {
            throw new InvalidArgumentException("Ya existe un estado de conductor con el nombre '{$request->name}'.");
        }

        // Crear el modelo actualizado
        $model = new DriverStatusModel(
            id: $request->id,
            name: trim($request->name),
            active: $request->active
        );

        // Actualizar en el repositorio
        $updatedModel = $this->repository->update($model);

        // Retornar DTO de respuesta
        return DriverStatusResponseDTO::fromModel($updatedModel);
    }
}
