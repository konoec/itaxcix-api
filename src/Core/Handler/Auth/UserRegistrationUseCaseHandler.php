<?php

namespace itaxcix\Core\Handler\Auth;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Domain\user\CitizenProfileModel;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Domain\user\UserCodeModel;
use itaxcix\Core\Domain\user\UserContactModel;
use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Core\Domain\user\UserRoleModel;
use itaxcix\Core\Domain\vehicle\VehicleUserModel;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Auth\UserRegistrationUseCase;
use itaxcix\Infrastructure\Notifications\NotificationServiceFactory;
use itaxcix\Shared\DTO\useCases\Auth\RegistrationRequestDTO;

class UserRegistrationUseCaseHandler implements UserRegistrationUseCase {
    private PersonRepositoryInterface $personRepository;
    private UserRepositoryInterface $userRepository;
    private VehicleRepositoryInterface $vehicleRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private ContactTypeRepositoryInterface $contactTypeRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private UserStatusRepositoryInterface $userStatusRepository;
    private RoleRepositoryInterface $roleRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    private UserCodeTypeRepositoryInterface $userCodeTypeRepository;
    private UserCodeRepositoryInterface $userCodeRepository;
    private NotificationServiceFactory $notificationServiceFactory;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private DriverStatusRepositoryInterface $driverStatusRepository;
    private CitizenProfileRepositoryInterface $citizenProfileRepository;

    public function __construct(PersonRepositoryInterface $personRepository, UserRepositoryInterface $userRepository, VehicleRepositoryInterface $vehicleRepository, VehicleUserRepositoryInterface $vehicleUserRepository, ContactTypeRepositoryInterface $contactTypeRepository, UserContactRepositoryInterface $userContactRepository, UserStatusRepositoryInterface $userStatusRepository, RoleRepositoryInterface $roleRepository, UserRoleRepositoryInterface $userRoleRepository, UserCodeTypeRepositoryInterface $userCodeTypeRepository, UserCodeRepositoryInterface $userCodeRepository, NotificationServiceFactory $notificationServiceFactory, DriverProfileRepositoryInterface $driverProfileRepository, DriverStatusRepositoryInterface $driverStatusRepository, CitizenProfileRepositoryInterface $citizenProfileRepository)
    {
        $this->personRepository = $personRepository;
        $this->userRepository = $userRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->contactTypeRepository = $contactTypeRepository;
        $this->userContactRepository = $userContactRepository;
        $this->userStatusRepository = $userStatusRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userCodeTypeRepository = $userCodeTypeRepository;
        $this->userCodeRepository = $userCodeRepository;
        $this->notificationServiceFactory = $notificationServiceFactory;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->driverStatusRepository = $driverStatusRepository;
        $this->citizenProfileRepository = $citizenProfileRepository;
    }

    /**
     * @throws \Exception
     */
    public function execute(RegistrationRequestDTO $dto): ?array
    {
        // Validar que existan los roles requeridos y estén activos
        $roleConductor = $this->roleRepository->findRoleByName("CONDUCTOR");
        $roleCiudadano = $this->roleRepository->findRoleByName("CIUDADANO");
        if (!$roleConductor || !$roleConductor->isActive()) {
            throw new InvalidArgumentException('El rol CONDUCTOR no existe o no está activo.');
        }
        if (!$roleCiudadano || !$roleCiudadano->isActive()) {
            throw new InvalidArgumentException('El rol CIUDADANO no existe o no está activo.');
        }

        $contactType = $this->contactTypeRepository->findContactTypeById($dto->contactTypeId);
        $person = $this->personRepository->findPersonById($dto->personId);
        $existingUser = $this->userRepository->findAllUserByPersonId($dto->personId);
        $userContact = $this->userContactRepository->findAllUserContactByValue($dto->contactValue);
        $userStatus = $this->userStatusRepository->findUserStatusByName('ACTIVO');
        $userCodeType = $this->userCodeTypeRepository->findUserCodeTypeByName('VERIFICACIÓN');

        if (!$userCodeType) {
            throw new InvalidArgumentException('El tipo de código de usuario no existe.');
        }

        if (!$contactType) {
            throw new InvalidArgumentException('El tipo de contacto activo no existe.');
        }

        if ($userContact){
            throw new InvalidArgumentException('Ya existe un contacto admitido registrado con ese número o correo.');
        }

        if (!$person) {
            throw new InvalidArgumentException('No existe una persona activa con ese ID.');
        }

        if ($person->getValidationDate() === null) {
            throw new InvalidArgumentException('La persona no ha sido validada biométricamente.');
        }

        $validationDate = $person->getValidationDate();

        if ($validationDate !== null) {
            $now = new DateTime();
            $diff = $now->getTimestamp() - $validationDate->getTimestamp();
            if ($diff > 300) { // 300 segundos = 5 minutos
                throw new InvalidArgumentException('La validación biométrica ha expirado. Debe validarse nuevamente.');
            }
        }

        if (!$userStatus) {
            throw new InvalidArgumentException('El estado de usuario activo no existe.');
        }

        // Determinar el rol objetivo basado en si es conductor o ciudadano
        $targetRole = $dto->vehicleId !== null ? $roleConductor : $roleCiudadano;
        $roleType = $dto->vehicleId !== null ? "CONDUCTOR" : "CIUDADANO";

        // Verificar si el usuario ya existe
        if ($existingUser) {
            // El usuario ya existe, verificar si ya tiene el rol que intenta registrar
            $existingRoles = $this->userRoleRepository->findActiveRolesByUserId($existingUser->getId());

            foreach ($existingRoles as $userRole) {
                if ($userRole->getRole()->getName() === $roleType) {
                    throw new InvalidArgumentException("El usuario ya tiene el rol de $roleType asignado.");
                }
            }

            // Verificar si ya tiene un perfil del tipo que intenta crear
            if ($dto->vehicleId !== null) {
                // Verificando si ya es conductor
                $existingDriverProfile = $this->driverProfileRepository->findDriverProfileByUserId($existingUser->getId());
                if ($existingDriverProfile) {
                    throw new InvalidArgumentException('El usuario ya tiene un perfil de conductor.');
                }
            } else {
                // Verificando si ya es ciudadano
                $existingCitizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($existingUser->getId());
                if ($existingCitizenProfile) {
                    throw new InvalidArgumentException('El usuario ya tiene un perfil de ciudadano.');
                }
            }

            // El usuario existe pero no tiene este rol, proceder a agregarlo
            $user = $existingUser;
        } else {
            // El usuario no existe, crear uno nuevo (flujo original)
            $user = new UserModel(
                id: null,
                password: password_hash($dto->password, PASSWORD_DEFAULT),
                person: $person,
                status: $userStatus
            );

            $user = $this->userRepository->saveUser($user);
        }

        // Validaciones específicas para conductor
        $driverStatus = null;
        $vehicle = null;

        if ($dto->vehicleId !== null) {
            $vehicle = $this->vehicleRepository->findVehicleById($dto->vehicleId);
            $vehicleUser = $this->vehicleUserRepository->findVehicleUserByVehicleId($dto->vehicleId);
            $driverStatus = $this->driverStatusRepository->findDriverStatusByName('PENDIENTE');

            if (!$vehicle) {
                throw new InvalidArgumentException('El vehículo no existe o no está activo.');
            }

            if ($vehicleUser) {
                throw new InvalidArgumentException('El vehículo ya está registrado a otro usuario.');
            }

            if (!$driverStatus) {
                throw new InvalidArgumentException('El estado de conductor pendiente no existe.');
            }
        }

        // Crear o actualizar contacto del usuario
        $newUserContact = new UserContactModel(
            id: null,
            user: $user,
            type: $contactType,
            value: $dto->contactValue,
            confirmed: false,
            active: true
        );

        $newUserContact = $this->userContactRepository->saveUserContact($newUserContact);

        // Crear el perfil correspondiente
        if ($dto->vehicleId !== null) {
            // Crear perfil de conductor
            $driverProfile = new DriverProfileModel(
                id: null,
                user: $user,
                status: $driverStatus,
                averageRating: 0.00,
                ratingCount: 0
            );

            $this->driverProfileRepository->saveDriverProfile($driverProfile);

            // Asociar vehículo al usuario
            $newVehicleUser = new VehicleUserModel(
                id: null,
                user: $user,
                vehicle: $vehicle,
                active: true
            );

            $this->vehicleUserRepository->saveVehicleUser($newVehicleUser);
        } else {
            // Crear perfil de ciudadano
            $citizenProfile = new CitizenProfileModel(
                id: null,
                user: $user,
                averageRating: 0.00,
                ratingCount: 0
            );

            $this->citizenProfileRepository->saveCitizenProfile($citizenProfile);
        }

        // Asignar el nuevo rol al usuario
        $userRole = new UserRoleModel(
            id: null,
            role: $targetRole,
            user: $user,
            active: true
        );

        $this->userRoleRepository->saveUserRole($userRole);

        // Crear código de verificación
        $newUserCode = new UserCodeModel(
            id: null,
            type: $userCodeType,
            contact: $newUserContact,
            code: $this->generateUserCode(),
            expirationDate: (new DateTime())->modify('+5 minutes'),
            useDate: null,
            used: false
        );

        $newUserCode = $this->userCodeRepository->saveUserCode($newUserCode);

        // Enviar notificación
        $service = $this->notificationServiceFactory->getServiceForContactType($dto->contactTypeId);
        $service->send($newUserCode->getContact()->getValue(), 'Código de verificación', $newUserCode->getCode(), 'verification');

        return ['userId' => $user->getId()];
    }

    private function generateUserCode(int $length = 6): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }
}