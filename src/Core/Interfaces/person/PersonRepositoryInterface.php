<?php

namespace itaxcix\Core\Interfaces\person;

use DateTime;
use itaxcix\Core\Domain\person\PersonModel;

interface PersonRepositoryInterface
{
    public function findAllPersonByDocument(string $documentValue): ?PersonModel;
    public function findPersonById(int $personId): ?PersonModel;
    public function savePerson(PersonModel $personModel): PersonModel;
}