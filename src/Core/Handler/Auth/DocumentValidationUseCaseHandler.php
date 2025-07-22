<?php

namespace itaxcix\Core\Handler\Auth;

use InvalidArgumentException;
use itaxcix\Core\Domain\person\PersonModel;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\user\AdminProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\UseCases\Auth\DocumentValidationUseCase;
use itaxcix\Shared\DTO\useCases\Auth\DocumentValidationRequestDTO;

class DocumentValidationUseCaseHandler implements DocumentValidationUseCase
{
    private PersonRepositoryInterface $personRepository;
    private DocumentTypeRepositoryInterface $documentTypeRepository;
    private UserRepositoryInterface $userRepository;
    private AdminProfileRepositoryInterface $adminProfileRepository;

    public function __construct(PersonRepositoryInterface $personRepository, DocumentTypeRepositoryInterface $documentTypeRepository, UserRepositoryInterface $userRepository, AdminProfileRepositoryInterface $adminProfileRepository)
    {
        $this->personRepository = $personRepository;
        $this->documentTypeRepository = $documentTypeRepository;
        $this->userRepository = $userRepository;
        $this->adminProfileRepository = $adminProfileRepository;
    }

    public function execute(DocumentValidationRequestDTO $dto): ?array
    {
        $documentType = $this->documentTypeRepository->findDocumentTypeByName('DNI');

        if ($documentType === null) {
            throw new InvalidArgumentException('El tipo de documento no existe.');
        }

        if ($dto->documentTypeId !== $documentType->getId()) {
            throw new InvalidArgumentException('El tipo de documento no es admitido actualmente.');
        }

        $person = $this->personRepository->findAllPersonByDocument($dto->documentValue, $dto->documentTypeId);

        if ($person) {

            if ($person->isActive() === false) {
                throw new InvalidArgumentException('El documento ingresado hace referencia a una persona con acceso restringido. Contacte al administrador.');
            }

            $user = $this->userRepository->findAllUserByPersonDocument($person->getDocument());

            $adminProfile = $this->adminProfileRepository->findAdminProfileByUserId($user?->getId());

            if ($adminProfile !== null) {
                return [
                    'personId' => $person->getId()
                ];
            }

            if ($user !== null) {
                throw new InvalidArgumentException('Ya existe un usuario con ese documento.');
            }

            return [
                'personId' => $person->getId()
            ];
        }

        $data = $this->fakeReniecApi($dto->documentValue);

        $person = new PersonModel(
            id: null,
            name: $data['name'] ?? '',
            lastName: $data['lastName'] ?? '',
            documentType: $documentType,
            document: $dto->documentValue,
            validationDate: null,
            image: null,
            active: true
        );

        $person = $this->personRepository->savePerson($person);

        return [
            'personId' => $person->getId()
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