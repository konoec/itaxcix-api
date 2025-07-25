<?php

namespace itaxcix\Core\Interfaces\person;

use itaxcix\Core\Domain\person\PersonModel;

interface PersonRepositoryInterface
{
    public function findAllPersonByDocument(string $documentValue, int $documentTypeId): ?PersonModel;
    public function findPersonById(int $personId): ?PersonModel;
    public function findAllPersonById(int $personId): ?PersonModel;
    public function savePerson(PersonModel $personModel): PersonModel;
    public function findByDocumentTypeId(int $documentTypeId): bool;
}