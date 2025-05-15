<?php

namespace itaxcix\services;

use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use InvalidArgumentException;
use itaxcix\models\entities\tuc\EstadoTuc;
use itaxcix\models\entities\tuc\TramiteTuc;
use itaxcix\models\entities\usuario\PerfilConductor;
use itaxcix\models\entities\usuario\Usuario;
use itaxcix\models\entities\usuario\VehiculoUsuario;
use RuntimeException;

class DriverService {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    private function getPerfilConductorRepository(): EntityRepository {
        return $this->entityManager->getRepository(PerfilConductor::class);
    }

    private function getUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(Usuario::class);
    }

    private function getVehiculoUsuarioRepository(): EntityRepository {
        return $this->entityManager->getRepository(VehiculoUsuario::class);
    }

    private function getEstadoTucRepository(): EntityRepository {
        return $this->entityManager->getRepository(EstadoTuc::class);
    }

    private function getTramiteTucRepository(): EntityRepository {
        return $this->entityManager->getRepository(TramiteTuc::class);
    }

    public function activateAvailability(int $userId): void {
        // 1. Obtener usuario
        $usuario = $this->getUsuarioRepository()->find($userId);

        if (!$usuario) {
            throw new InvalidArgumentException("Conductor no encontrado", 400);
        }

        // 2. Verificar si tiene vehículos activos asignados
        $vehiculosActivos = $this->getVehiculoUsuarioRepository()->findBy([
            'usuario' => $usuario,
            'activo' => true
        ]);

        if (empty($vehiculosActivos)) {
            throw new RuntimeException("El conductor no tiene ningún vehículo activo asignado.", 400);
        }

        // 3. Obtener estado TUC "Activo"
        $estadoTucActivo = $this->getEstadoTucRepository()->getByNombre('Activo');

        if (!$estadoTucActivo) {
            throw new RuntimeException("No se encontró el estado 'Activo' para los trámites TUC.", 500);
        }

        // 4. Buscar entre todos los vehículos activos, aquellos con TUC vigente
        $tramitesVigentes = [];

        foreach ($vehiculosActivos as $vehiculoUsuario) {
            $vehiculo = $vehiculoUsuario->getVehiculo();

            // Usamos el nuevo método del repositorio
            $tramitesVigentes = $this->getTramiteTucRepository()->findVigentesByVehiculo($vehiculo, $estadoTucActivo);

            if (!empty($tramitesVigentes)) {
                break; // Encontramos al menos uno válido
            }
        }

        if (empty($tramitesVigentes)) {
            throw new RuntimeException("El conductor no tiene ningún vehículo con un trámite TUC vigente.", 400);
        }

        // 5. Activar disponibilidad del conductor
        $perfil = $this->getPerfilConductorRepository()->findOneBy(['usuario' => $usuario]);

        if (!$perfil) {
            throw new RuntimeException("Perfil de conductor no encontrado", 400);
        }

        $perfil->setDisponible(true);
        $this->getPerfilConductorRepository()->save($perfil);
    }

    public function deactivateAvailability(int $userId): void {
        $perfil = $this->getPerfilConductorRepository()->findOneBy(['usuario' => $userId]);

        if (!$perfil) {
            throw new Exception('Conductor no encontrado', 400);
        }

        $perfil->setDisponible(false);
        $this->getPerfilConductorRepository()->save($perfil);
    }

    /**
     * Obtiene el estado de disponibilidad del conductor.
     *
     * @param int $userId
     * @return array ['available' => bool, 'updatedAt' => string (ISO 8601)]
     * @throws Exception
     */
    public function getDriverStatus(int $userId): array {
        // Buscar el perfil del conductor
        $perfil = $this->getPerfilConductorRepository()->findOneByUserId($userId);

        if (!$perfil) {
            throw new Exception("Conductor no encontrado", 404);
        }

        return [
            'available' => $perfil->isDisponible(),
            'updatedAt' => null
        ];
    }
}