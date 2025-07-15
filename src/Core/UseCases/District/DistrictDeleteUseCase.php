<?php

namespace itaxcix\Core\UseCases\District;

use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use RuntimeException;

class DistrictDeleteUseCase
{
    private DistrictRepositoryInterface $districtRepository;

    public function __construct(DistrictRepositoryInterface $districtRepository)
    {
        $this->districtRepository = $districtRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que el distrito existe
        $district = $this->districtRepository->findById($id);
        if (!$district) {
            throw new RuntimeException("District with ID {$id} not found");
        }

        $result = $this->districtRepository->delete($id);

        if ($result) {
            $result = [
                'message' => 'Distrito eliminado correctamente.'
            ];
        }else{
            throw new RuntimeException("Error al eliminar el distrito con ID {$id}");
        }

        // Eliminar del repositorio
        return $result;
    }
}
