<?php

namespace itaxcix\Core\Handler\Auth;

use InvalidArgumentException;
use itaxcix\Core\Domain\person\PersonModel;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\UseCases\Auth\DocumentValidationUseCase;
use itaxcix\Shared\DTO\useCases\Auth\DocumentValidationRequestDTO;

class DocumentValidationUseCaseHandler implements DocumentValidationUseCase
{
    private PersonRepositoryInterface $personRepository;
    private DocumentTypeRepositoryInterface $documentTypeRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(PersonRepositoryInterface $personRepository, DocumentTypeRepositoryInterface $documentTypeRepository, UserRepositoryInterface $userRepository)
    {
        $this->personRepository = $personRepository;
        $this->documentTypeRepository = $documentTypeRepository;
        $this->userRepository = $userRepository;
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

        $person = $this->personRepository->findAllPersonByDocument($dto->documentValue);

        if ($person) {

            if ($person->isActive() === false) {
                throw new InvalidArgumentException('El documento ingresado hace referencia a una persona con acceso restringido. Contacte al administrador.');
            }

            $user = $this->userRepository->findAllUserByPersonDocument($person->getDocument());

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
        return [
            'name' => 'PATRICIO JESUS',
            'lastName' => 'GONZALES GONZALES',
        ];
    }
}