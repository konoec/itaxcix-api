<?php

namespace itaxcix\Core\UseCases\Admin\User;

use InvalidArgumentException;
use DateTime;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\AdminProfileRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\person\PersonEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity;
use itaxcix\Infrastructure\Database\Entity\user\AdminProfileEntity;
use itaxcix\Shared\DTO\Admin\User\CreateAdminUserRequestDTO;

/**
 * CreateAdminUserUseCase - Caso de uso para crear usuarios administradores
 *
 * Flujo integral que crea:
 * 1. Persona
 * 2. Usuario
 * 3. Contacto (email confirmado)
 * 4. Rol de administrador
 * 5. Perfil de administrador
 *
 * Realiza todas las validaciones antes de crear cualquier entidad.
 */
class CreateAdminUserUseCase
{
    private PersonRepositoryInterface $personRepository;
    private DocumentTypeRepositoryInterface $documentTypeRepository;
    private UserRepositoryInterface $userRepository;
    private UserStatusRepositoryInterface $userStatusRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private ContactTypeRepositoryInterface $contactTypeRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    private RoleRepositoryInterface $roleRepository;
    private AdminProfileRepositoryInterface $adminProfileRepository;

    public function __construct(
        PersonRepositoryInterface $personRepository,
        DocumentTypeRepositoryInterface $documentTypeRepository,
        UserRepositoryInterface $userRepository,
        UserStatusRepositoryInterface $userStatusRepository,
        UserContactRepositoryInterface $userContactRepository,
        ContactTypeRepositoryInterface $contactTypeRepository,
        UserRoleRepositoryInterface $userRoleRepository,
        RoleRepositoryInterface $roleRepository,
        AdminProfileRepositoryInterface $adminProfileRepository
    ) {
        $this->personRepository = $personRepository;
        $this->documentTypeRepository = $documentTypeRepository;
        $this->userRepository = $userRepository;
        $this->userStatusRepository = $userStatusRepository;
        $this->userContactRepository = $userContactRepository;
        $this->contactTypeRepository = $contactTypeRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->roleRepository = $roleRepository;
        $this->adminProfileRepository = $adminProfileRepository;
    }

    public function execute(CreateAdminUserRequestDTO $request): array
    {
        // 1. Validar que el tipo de documento DNI existe
        $documentType = $this->documentTypeRepository->findDocumentTypeByName('DNI');
        if (!$documentType) {
            throw new InvalidArgumentException("Tipo de documento DNI no encontrado en el sistema");
        }

        // 2. Verificar que no existe usuario con ese documento
        $existingUser = $this->userRepository->findUserByPersonDocument($request->document);
        if ($existingUser) {
            throw new InvalidArgumentException("Ya existe un usuario con el documento {$request->document}");
        }

        // 3. Verificar que no existe contacto con ese email
        $existingContact = $this->userContactRepository->findAllUserContactByValue($request->email);
        if ($existingContact) {
            throw new InvalidArgumentException("Ya existe un usuario con el email {$request->email}");
        }

        // 4. Validar que existe estado ACTIVO para usuarios
        $activeStatus = $this->userStatusRepository->findUserStatusByName('ACTIVO');
        if (!$activeStatus) {
            throw new InvalidArgumentException("Estado ACTIVO no encontrado en el sistema");
        }

        // 5. Validar que existe tipo de contacto EMAIL
        $emailContactType = $this->contactTypeRepository->findContactTypeByName('CORREO ELECTRÓNICO');
        if (!$emailContactType) {
            throw new InvalidArgumentException("Tipo de contacto CORREO ELECTRÓNICO no encontrado");
        }

        // 6. Validar que existe rol ADMINISTRADOR para web
        $adminRole = $this->roleRepository->findRoleByName('ADMINISTRADOR');
        if (!$adminRole || !$adminRole->isWeb() || !$adminRole->isActive()) {
            throw new InvalidArgumentException("Rol ADMINISTRADOR web no encontrado o no está activo");
        }

        // 7. Obtener nombre y apellido desde fake API
        $personData = $this->fakeReniecApi($request->document);

        // 8. INICIAR CREACIÓN DE ENTIDADES (todo validado)
        // Crear directamente el modelo de dominio PersonModel
        $personModel = new \itaxcix\Core\Domain\person\PersonModel(
            id: null, // ID será asignado por la base de datos
            name: $personData['name'],
            lastName: $personData['lastName'],
            documentType: $documentType,
            document: $request->document,
            validationDate: new DateTime(),
            image: null,
            active: true
        );

        // Guardar persona
        $savedPersonModel = $this->personRepository->savePerson($personModel);

        // Crear directamente el modelo de dominio UserModel
        $userModel = new \itaxcix\Core\Domain\user\UserModel(
            id: null, // ID será asignado por la base de datos
            password: password_hash($request->password, PASSWORD_DEFAULT),
            person: $savedPersonModel,
            status: $activeStatus
        );

        // Guardar usuario
        $savedUserModel = $this->userRepository->saveUser($userModel);

        // Crear directamente el modelo de dominio UserContactModel
        $contactModel = new \itaxcix\Core\Domain\user\UserContactModel(
            id: null, // ID será asignado por la base de datos
            user: $savedUserModel,
            type: $emailContactType,
            value: $request->email,
            confirmed: true, // Auto-confirmado para admins
            active: true
        );

        // Guardar contacto
        $savedContactModel = $this->userContactRepository->saveUserContact($contactModel);

        // Crear directamente el modelo de dominio UserRoleModel
        $userRoleModel = new \itaxcix\Core\Domain\user\UserRoleModel(
            id: null, // ID será asignado por la base de datos
            role: $adminRole,
            user: $savedUserModel,
            active: true
        );

        // Crear directamente el modelo de dominio AdminProfileModel
        $adminProfileModel = new \itaxcix\Core\Domain\user\AdminProfileModel(
            id: null, // ID será asignado por la base de datos
            user: $savedUserModel,
            area: $request->area,
            position: $request->position
        );

        // Guardar perfil de administrador
        $savedAdminProfileModel = $this->adminProfileRepository->saveAdminProfile($adminProfileModel);

        return [
            'message' => 'Usuario administrador creado exitosamente',
            'user' => [
                'id' => $savedUserModel->getId(),
                'firstName' => $savedPersonModel->getName(),
                'lastName' => $savedPersonModel->getLastName(),
                'document' => $savedPersonModel->getDocument(),
                'email' => $savedContactModel->getValue(),
                'role' => $adminRole->getName(),
                'area' => $savedAdminProfileModel->getArea(),
                'position' => $savedAdminProfileModel->getPosition()
            ]
        ];
    }

    private function fakeReniecApi(string $documentValue): array
    {
        $nombres = ['JUAN', 'MARIA', 'PATRICIO', 'LUIS', 'ANA', 'CARLOS', 'JESUS', 'SOFIA', 'MIGUEL', 'LAURA', 'ANDREA', 'PEDRO', 'JORGE', 'VALERIA', 'DANIEL', 'PAULA', 'MARTIN', 'CAMILA', 'ALBERTO', 'ELENA'];
        $apellidos = ['GONZALES', 'PEREZ', 'RAMIREZ', 'GARCIA', 'RODRIGUEZ', 'LOPEZ', 'FERNANDEZ', 'SANCHEZ', 'CASTILLO', 'MORALES', 'TORRES', 'RAMOS', 'CRUZ', 'DIAZ', 'VARGAS', 'REYES', 'FLORES', 'MEDINA', 'ROMERO', 'HERRERA'];

        $nombre = $nombres[array_rand($nombres)];
        $apellido1 = $apellidos[array_rand($apellidos)];
        $apellido2 = $apellidos[array_rand($apellidos)];

        return [
            'name' => $nombre,
            'lastName' => $apellido1 . ' ' . $apellido2,
        ];
    }
}
