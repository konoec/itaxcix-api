<?php

namespace itaxcix\Core\UseCases\Admin\User;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\AdminUserListRequestDTO;
use itaxcix\Shared\DTO\Admin\User\AdminUserListResponseDTO;

/**
 * AdminUserListUseCase - Caso de uso para listar usuarios con filtros administrativos
 *
 * Permite listar usuarios del sistema con filtros avanzados específicos para
 * administración, incluyendo estado de conductor, vehículos asociados,
 * contactos verificados, etc.
 */
class AdminUserListUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    private CitizenProfileRepositoryInterface $citizenProfileRepository;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CitizenProfileRepositoryInterface $citizenProfileRepository,
        DriverProfileRepositoryInterface $driverProfileRepository,
        UserContactRepositoryInterface $userContactRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->citizenProfileRepository = $citizenProfileRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->userContactRepository = $userContactRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(AdminUserListRequestDTO $request): AdminUserListResponseDTO
    {
        // Construir filtros complejos basados en la estructura existente
        $filters = [
            'search' => $request->search, // Buscar en persona.name, lastName, document
            'statusId' => $request->statusId,
            'roleId' => $request->roleId,
            'userType' => $request->userType,
            'driverStatus' => $request->driverStatus,
            'hasVehicle' => $request->hasVehicle,
            'contactVerified' => $request->contactVerified
        ];

        // Obtener usuarios paginados con todos los filtros
        $usersData = $this->userRepository->findUsersPaginatedWithFilters(
            page: $request->page,
            limit: $request->limit,
            filters: $filters
        );

        $enrichedUsers = [];
        foreach ($usersData['users'] as $user) {
            $enrichedUsers[] = $this->enrichUserData($user);
        }

        return new AdminUserListResponseDTO(
            users: $enrichedUsers,
            total: $usersData['total'],
            page: $request->page,
            limit: $request->limit
        );
    }

    private function enrichUserData($user): array
    {
        // Obtener información completa del usuario incluyendo:
        // - Datos de persona (nombre, documento)
        // - Estado del usuario
        // - Perfiles (ciudadano/conductor)
        // - Contactos y su estado de verificación
        // - Vehículo asociado (si aplica)
        // - Roles asignados

        $enrichedData = [
            'id' => $user->getId(),
            'person' => [
                'id' => $user->getPerson()->getId(),
                'name' => $user->getPerson()->getName(),
                'lastName' => $user->getPerson()->getLastName(),
                'document' => $user->getPerson()->getDocument(),
                'documentType' => $user->getPerson()->getDocumentType()->getName(),
                'validationDate' => $user->getPerson()->getValidationDate(),
                'active' => $user->getPerson()->isActive()
            ],
            'status' => [
                'id' => $user->getStatus()->getId(),
                'name' => $user->getStatus()->getName()
            ],
            'profiles' => [],
            'contacts' => [],
            'vehicle' => null,
            'roles' => []
        ];

        // Obtener perfil de ciudadano si existe
        $citizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($user->getId());
        if ($citizenProfile) {
            $enrichedData['profiles']['citizen'] = [
                'id' => $citizenProfile->getId(),
                'averageRating' => $citizenProfile->getAverageRating(),
                'ratingCount' => $citizenProfile->getRatingCount()
            ];
        }

        // Obtener perfil de conductor si existe
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($user->getId());
        if ($driverProfile) {
            $enrichedData['profiles']['driver'] = [
                'id' => $driverProfile->getId(),
                'status' => $driverProfile->getStatus()->getName(),
                'averageRating' => $driverProfile->getAverageRating(),
                'ratingCount' => $driverProfile->getRatingCount()
            ];
        }

        // Obtener contactos y su estado de verificación
        $contacts = $this->userContactRepository->findAllUserContactByUserId($user->getId());
        foreach ($contacts as $contact) {
            $enrichedData['contacts'][] = [
                'id' => $contact->getId(),
                'type' => $contact->getType()->getName(),
                'value' => $contact->getValue(),
                'confirmed' => $contact->isConfirmed(),
                'active' => $contact->isActive()
            ];
        }

        // Obtener vehículo asociado si existe
        $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($user->getId());
        if ($vehicleUser && $vehicleUser->isActive()) {
            $vehicle = $vehicleUser->getVehicle();
            $enrichedData['vehicle'] = [
                'id' => $vehicle->getId(),
                'plate' => $vehicle->getLicensePlate(),
                'brand' => $vehicle->getModel() && $vehicle->getModel()->getBrand() ? [
                    'id' => $vehicle->getModel()->getBrand()->getId(),
                    'name' => $vehicle->getModel()->getBrand()->getName()
                ] : null,
                'model' => $vehicle->getModel() ? [
                    'id' => $vehicle->getModel()->getId(),
                    'name' => $vehicle->getModel()->getName()
                ] : null,
                'color' => $vehicle->getColor() ? [
                    'id' => $vehicle->getColor()->getId(),
                    'name' => $vehicle->getColor()->getName()
                ] : null
            ];
        }

        // Obtener roles activos del usuario
        $roles = $this->userRoleRepository->findActiveRolesByUserId($user->getId());
        $enrichedData['roles'] = array_map(function($userRole) {
            $role = $userRole->getRole();
            return [
                'id' => $role->getId(),
                'name' => $role->getName(),
                'active' => $role->isActive(),
                'web' => $role->isWeb()
            ];
        }, $roles);

        // Inicializar perfiles aunque sean null
        if (!isset($enrichedData['profiles']['citizen'])) {
            $enrichedData['profiles']['citizen'] = null;
        }
        if (!isset($enrichedData['profiles']['driver'])) {
            $enrichedData['profiles']['driver'] = null;
        }

        return $enrichedData;
    }
}
