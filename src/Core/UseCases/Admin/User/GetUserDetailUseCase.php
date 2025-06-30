<?php

namespace itaxcix\Core\UseCases\Admin\User;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\AdminUserDetailResponseDTO;

/**
 * GetUserDetailUseCase - Caso de uso para obtener detalles completos de un usuario
 *
 * Proporciona información completa de un usuario incluyendo persona, perfiles,
 * contactos, vehículos, roles y historial de admisión.
 */
class GetUserDetailUseCase
{
    private UserRepositoryInterface $userRepository;
    private CitizenProfileRepositoryInterface $citizenProfileRepository;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    public function __construct(
        UserRepositoryInterface $userRepository,
        CitizenProfileRepositoryInterface $citizenProfileRepository,
        DriverProfileRepositoryInterface $driverProfileRepository,
        UserContactRepositoryInterface $userContactRepository,
        UserRoleRepositoryInterface $userRoleRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository
    ) {
        $this->userRepository = $userRepository;
        $this->citizenProfileRepository = $citizenProfileRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->userContactRepository = $userContactRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
    }

    public function execute(int $userId): AdminUserDetailResponseDTO
    {
        // Validar que el usuario existe
        $user = $this->userRepository->findUserById($userId);
        if (!$user) {
            throw new InvalidArgumentException("Usuario con ID {$userId} no encontrado");
        }

        // Obtener datos de la persona
        $person = $user->getPerson();
        $personData = [
            'id' => $person->getId(),
            'name' => $person->getName(),
            'lastName' => $person->getLastName(),
            'document' => $person->getDocument(),
            'documentType' => [
                'id' => $person->getDocumentType()->getId(),
                'name' => $person->getDocumentType()->getName()
            ],
            'validationDate' => $person->getValidationDate()?->format('Y-m-d H:i:s'),
            'image' => $person->getImage(),
            'active' => $person->isActive()
        ];

        // Obtener estado del usuario
        $userStatusData = [
            'id' => $user->getStatus()->getId(),
            'name' => $user->getStatus()->getName()
        ];

        // Obtener contactos del usuario
        $contacts = $this->userContactRepository->findAllUserContactByUserId($userId);
        $contactsData = [];
        foreach ($contacts as $contact) {
            $contactsData[] = [
                'id' => $contact->getId(),
                'type' => [
                    'id' => $contact->getType()->getId(),
                    'name' => $contact->getType()->getName()
                ],
                'value' => $contact->getValue(),
                'confirmed' => $contact->isConfirmed(),
                'active' => $contact->isActive()
            ];
        }

        // Obtener roles del usuario
        $userRoles = $this->userRoleRepository->findUserRolesByUserId($userId);
        $rolesData = [];
        foreach ($userRoles as $userRole) {
            if ($userRole->isActive()) {
                $role = $userRole->getRole();
                $rolesData[] = [
                    'id' => $role->getId(),
                    'name' => $role->getName(),
                    'active' => $role->isActive(),
                    'web' => $role->isWeb()
                ];
            }
        }

        // Obtener perfil de ciudadano si existe
        $citizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($userId);
        $citizenProfileData = null;
        if ($citizenProfile) {
            $citizenProfileData = [
                'id' => $citizenProfile->getId(),
                'averageRating' => $citizenProfile->getAverageRating(),
                'ratingCount' => $citizenProfile->getRatingCount()
            ];
        }

        // Obtener perfil de conductor si existe
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($userId);
        $driverProfileData = null;
        if ($driverProfile) {
            $driverProfileData = [
                'id' => $driverProfile->getId(),
                'status' => [
                    'id' => $driverProfile->getStatus()->getId(),
                    'name' => $driverProfile->getStatus()->getName()
                ],
                'averageRating' => $driverProfile->getAverageRating(),
                'ratingCount' => $driverProfile->getRatingCount()
            ];
        }

        // Obtener vehículo asociado si existe
        $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($userId);
        $vehicleData = null;
        if ($vehicleUser && $vehicleUser->isActive()) {
            $vehicle = $vehicleUser->getVehicle();
            $vehicleData = [
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
                'year' => $vehicle->getManufactureYear(),
                'color' => $vehicle->getColor() ? [
                    'id' => $vehicle->getColor()->getId(),
                'active' => $vehicle->isActive()
            ] : null
            ];
        }

        return new AdminUserDetailResponseDTO(
            userId: $userId,
            person: $personData,
            userStatus: $userStatusData,
            contacts: $contactsData,
            roles: $rolesData,
            citizenProfile: $citizenProfileData,
            driverProfile: $driverProfileData,
            vehicle: $vehicleData
        );
    }
}
