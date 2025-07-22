<?php

namespace itaxcix\Core\UseCases\DriverStatus;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;

class DriverStatusDeleteUseCase
{
    private DriverStatusRepositoryInterface $repository;
    private DriverProfileRepositoryInterface $profileRepository;

    public function __construct(DriverStatusRepositoryInterface $repository, DriverProfileRepositoryInterface $profileRepository)
    {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
    }

    public function execute(int $id): array
    {
        // Validar que el registro existe
        $existingModel = $this->repository->findById($id);
        if (!$existingModel) {
            throw new InvalidArgumentException("No se encontró el estado de conductor con ID {$id}.");
        }

        // Verificar que no sea un estado crítico
        if (in_array($existingModel->getName(), ['APROBADO','RECHAZADO','PENDIENTE'])) {
            throw new InvalidArgumentException("No se puede eliminar el estado crítico: " . $existingModel->getName());
        }

        // Verificar si el estado está asociado a algún perfil de conductor
        $driversWithStatus = $this->profileRepository->findByStatusId($id);
        if ($driversWithStatus) {
            throw new InvalidArgumentException('No se puede eliminar el estado de conductor porque está asociado a uno o más perfiles de conductor.');
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
