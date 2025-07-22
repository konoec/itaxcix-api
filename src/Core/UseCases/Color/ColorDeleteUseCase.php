<?php

namespace itaxcix\Core\UseCases\Color;

use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;

class ColorDeleteUseCase
{
    private ColorRepositoryInterface $colorRepository;
    private VehicleRepositoryInterface $vehicleRepository;

    public function __construct(ColorRepositoryInterface $colorRepository, VehicleRepositoryInterface $vehicleRepository)
    {
        $this->colorRepository = $colorRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que el color existe antes de eliminarlo
        $existingColor = $this->colorRepository->findById($id);
        if (!$existingColor) {
            throw new \InvalidArgumentException("Color con ID {$id} no encontrado.");
        }

        // Verificar si el color está asociado a algún vehículo
        $vehicles = $this->vehicleRepository->findActiveByColorId($id);
        if (!empty($vehicles)) {
            throw new \InvalidArgumentException('No se puede eliminar el color porque está asociado a uno o más vehículos.');
        }

        return $this->colorRepository->delete($id);
    }
}
