<?php

namespace itaxcix\Core\UseCases\District;

use itaxcix\Core\Interfaces\location\CoordinatesRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use RuntimeException;

class DistrictDeleteUseCase
{
    private DistrictRepositoryInterface $districtRepository;
    private CoordinatesRepositoryInterface $coordinatesRepository;
    private TucProcedureRepositoryInterface $tucProcedureRepository;

    public function __construct(DistrictRepositoryInterface $districtRepository, CoordinatesRepositoryInterface $coordinatesRepository, TucProcedureRepositoryInterface $tucProcedureRepository)
    {
        $this->districtRepository = $districtRepository;
        $this->coordinatesRepository = $coordinatesRepository;
        $this->tucProcedureRepository = $tucProcedureRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que el distrito existe
        $district = $this->districtRepository->findById($id);
        if (!$district) {
            throw new RuntimeException("Distrito con ID {$id} no encontrado.");
        }

        // Verificar si el distrito está asociado a alguna coordenada
        $coordinates = $this->coordinatesRepository->findByDistrictId($id);
        if ($coordinates) {
            throw new RuntimeException('No se puede eliminar el distrito porque está asociado a una o más coordenadas.');
        }

        // Verificar si el distrito está asociado a algún procedimiento TUC
        $tucProcedures = $this->tucProcedureRepository->findByDistrictId($id);
        if ($tucProcedures) {
            throw new RuntimeException('No se puede eliminar el distrito porque está asociado a uno o más procedimientos TUC.');
        }

        // Eliminar del repositorio
        return $this->districtRepository->delete($id);
    }
}
