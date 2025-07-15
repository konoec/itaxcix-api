<?php

namespace itaxcix\Core\UseCases\DriverStatus;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\DriverStatusModel;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusResponseDTO;

class DriverStatusCreateUseCase
{
    private DriverStatusRepositoryInterface $repository;

    public function __construct(DriverStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DriverStatusRequestDTO $request): DriverStatusResponseDTO
    {
        // Validar que el nombre no exista
        if ($this->repository->existsByName($request->name)) {
            throw new InvalidArgumentException("Ya existe un estado de conductor con el nombre '{$request->name}'.");
        }

        // Crear el modelo
        $model = new DriverStatusModel(
            id: null,
            name: trim($request->name),
            active: $request->active
        );

        // Guardar en el repositorio
        $savedModel = $this->repository->create($model);

        // Retornar DTO de respuesta
        return DriverStatusResponseDTO::fromModel($savedModel);
    }
}
