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
    }

    /**
     * @throws \Exception
     */
    public function execute(RegistrationRequestDTO $dto): ?array
    {
        $contactType = $this->contactTypeRepository->findContactTypeById($dto->contactTypeId);
        $person = $this->personRepository->findPersonById($dto->personId);
        $user = $this->userRepository->findAllUserByPersonId($dto->personId);
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

        if ($user) {
            throw new InvalidArgumentException('Ya existe un usuario registrado con esa persona.');
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

        $newUser = new UserModel(
            id: null,
            password: password_hash($dto->password, PASSWORD_DEFAULT),
            person: $person,
            status: $userStatus
        );

        $newUser = $this->userRepository->saveUser($newUser);

        $newUserContact = new UserContactModel(
            id: null,
            user: $newUser,
            type: $contactType,
            value: $dto->contactValue,
            confirmed: false,
            active: true
        );

        $newUserContact = $this->userContactRepository->saveUserContact($newUserContact);
        $newVehicleUser = null;
        $role = null;

        if ($dto->vehicleId !== null) {
            $driverProfile = new DriverProfileModel(
                id: null,
                user: $newUser,
                available: true,
                status: $driverStatus,
                averageRating: 0.00,
                ratingCount:0
            );

            $driverProfile = $this->driverProfileRepository->saveDriverProfile($driverProfile);

            $newVehicleUser = new VehicleUserModel(
                id: null,
                user: $newUser,
                vehicle: $vehicle,
                active: true
            );

            $this->vehicleUserRepository->saveVehicleUser($newVehicleUser);

            // Asignar roles al usuario basado en el vehículo
            $role = $this->roleRepository->findRoleByName("CONDUCTOR");
        } else{
            $citizenProfile = new CitizenProfileModel(
                id: null,
                user: $newUser,
                averageRating: 0.00,
                ratingCount: 0
            );

            $citizenProfile = $this->citizenProfileRepository->saveCitizenProfile($citizenProfile);

            // Asignar roles al usuario basado en el ciudadano
            $role = $this->roleRepository->findRoleByName("CIUDADANO");
        }

        if (!$role) {
            throw new InvalidArgumentException('No se encontraron roles para asignar al usuario.');
        }

        $userRole = new UserRoleModel(
            id: null,
            role: $role,
            user: $newUser,
            active: true
        );

        $userRole = $this->userRoleRepository->saveUserRole($userRole);

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

        // Enviar correo de confirmación, aquí va el servicio
        $service = $this->notificationServiceFactory->getServiceForContactType($dto->contactTypeId);
        $service->send($newUserCode->getContact()->getValue(), 'Código de verificación', $newUserCode->getCode(), 'verification');

        return ['userId' => $newUser->getId()];
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