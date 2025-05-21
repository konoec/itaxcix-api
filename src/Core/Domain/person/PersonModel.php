<?php

namespace itaxcix\Core\Domain\person;

class PersonModel {
    private int $id;
    private ?string $name = null;
    private ?string $lastName = null;
    private ?DocumentTypeModel $documentType = null;
    private string $document;
    private bool $validated = true;
    private ?string $image = null;
    private bool $active = true;
}