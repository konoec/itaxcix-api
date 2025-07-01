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
        // 1. VALIDACIONES PREVIAS (sin crear nada en DB)
        $this->validateRequest($request);

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

        // 4. Validar que el tipo de documento existe
        $documentType = $this->documentTypeRepository->findById($request->documentTypeId);
        if (!$documentType) {
            throw new InvalidArgumentException("Tipo de documento con ID {$request->documentTypeId} no encontrado");
        }

        // 5. Validar que existe estado ACTIVO para usuarios
        $activeStatus = $this->userStatusRepository->findUserStatusByName('ACTIVO');
        if (!$activeStatus) {
            throw new InvalidArgumentException("Estado ACTIVO no encontrado en el sistema");
        }

        // 6. Validar que existe tipo de contacto EMAIL
        $emailContactType = $this->contactTypeRepository->findContactTypeByName('CORREO ELECTRÓNICO');
        if (!$emailContactType) {
            throw new InvalidArgumentException("Tipo de contacto CORREO ELECTRÓNICO no encontrado");
        }

        // 7. Validar que existe rol ADMINISTRADOR para web
        $adminRole = $this->roleRepository->findRoleByName('ADMINISTRADOR');
        if (!$adminRole || !$adminRole->isWeb() || !$adminRole->isActive()) {
            throw new InvalidArgumentException("Rol ADMINISTRADOR web no encontrado o no está activo");
        }

        // 8. INICIAR CREACIÓN DE ENTIDADES (todo validado)
        // Usamos el patrón de crear entidades directamente y usar repositorios para persistir

        // Crear persona entity
        $personEntity = new PersonEntity();
        $personEntity->setName($request->firstName);
        $personEntity->setLastName($request->lastName);
        $personEntity->setDocument($request->document);
        $personEntity->setDocumentType($documentType->toEntity());
        $personEntity->setActive(true);
        $personEntity->setValidationDate(new DateTime());

        // Convertir a modelo de dominio y guardar
        $personModel = $this->personRepository->toDomain($personEntity);
        $savedPersonModel = $this->personRepository->savePerson($personModel);

        // Crear usuario entity
        $userEntity = new UserEntity();
        $userEntity->setPerson($savedPersonModel->toEntity());
        $userEntity->setPassword(password_hash($request->password, PASSWORD_DEFAULT));
        $userEntity->setStatus($activeStatus->toEntity());

        // Convertir a modelo de dominio y guardar
        $userModel = $this->userRepository->toDomain($userEntity);
        $savedUserModel = $this->userRepository->saveUser($userModel);

        // Crear contacto entity
        $contactEntity = new UserContactEntity();
        $contactEntity->setUser($savedUserModel->toEntity());
        $contactEntity->setType($emailContactType->toEntity());
        $contactEntity->setValue($request->email);
        $contactEntity->setConfirmed(true); // Auto-confirmado para admins
        $contactEntity->setActive(true);

        // Convertir a modelo de dominio y guardar
        $contactModel = $this->userContactRepository->toDomain($contactEntity);
        $savedContactModel = $this->userContactRepository->saveUserContact($contactModel);

        // Asignar rol de administrador entity
        $userRoleEntity = new UserRoleEntity();
        $userRoleEntity->setUser($savedUserModel->toEntity());
        $userRoleEntity->setRole($adminRole->toEntity());
        $userRoleEntity->setActive(true);

        // Convertir a modelo de dominio y guardar
        $userRoleModel = $this->userRoleRepository->toDomain($userRoleEntity);
        $savedUserRoleModel = $this->userRoleRepository->saveUserRole($userRoleModel);

        // Crear perfil de administrador entity
        $adminProfileEntity = new AdminProfileEntity();
        $adminProfileEntity->setUser($savedUserModel->toEntity());
        $adminProfileEntity->setArea($request->area);
        $adminProfileEntity->setPosition($request->position);

        // Convertir a modelo de dominio y guardar
        $adminProfileModel = $this->adminProfileRepository->toDomain($adminProfileEntity);
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

    private function validateRequest(CreateAdminUserRequestDTO $request): void
    {
        // Validar campos requeridos
        if (empty(trim($request->firstName))) {
            throw new InvalidArgumentException("El nombre es requerido");
        }

        if (empty(trim($request->lastName))) {
            throw new InvalidArgumentException("El apellido es requerido");
        }

        if (empty(trim($request->document))) {
            throw new InvalidArgumentException("El documento es requerido");
        }

        if (empty(trim($request->email))) {
            throw new InvalidArgumentException("El email es requerido");
        }

        if (empty(trim($request->password))) {
            throw new InvalidArgumentException("La contraseña es requerida");
        }

        if (empty(trim($request->area))) {
            throw new InvalidArgumentException("El área es requerida");
        }

        if (empty(trim($request->position))) {
            throw new InvalidArgumentException("El cargo es requerido");
        }

        // Validar formato de email
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El formato del email es inválido");
        }

        // Validar longitud de contraseña
        if (strlen($request->password) < 8) {
            throw new InvalidArgumentException("La contraseña debe tener al menos 8 caracteres");
        }

        // Validar que la contraseña tenga al menos una mayúscula, una minúscula y un número
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $request->password)) {
            throw new InvalidArgumentException("La contraseña debe contener al menos una mayúscula, una minúscula y un número");
        }

        // Validar longitud de campos
        if (strlen($request->firstName) > 100) {
            throw new InvalidArgumentException("El nombre no puede superar los 100 caracteres");
        }

        if (strlen($request->lastName) > 100) {
            throw new InvalidArgumentException("El apellido no puede superar los 100 caracteres");
        }

        if (strlen($request->document) > 20) {
            throw new InvalidArgumentException("El documento no puede superar los 20 caracteres");
        }

        if (strlen($request->email) > 100) {
            throw new InvalidArgumentException("El email no puede superar los 100 caracteres");
        }

        if (strlen($request->area) > 100) {
            throw new InvalidArgumentException("El área no puede superar los 100 caracteres");
        }

        if (strlen($request->position) > 100) {
            throw new InvalidArgumentException("El cargo no puede superar los 100 caracteres");
        }
    }
}
