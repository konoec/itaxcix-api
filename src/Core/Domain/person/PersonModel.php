<?php

namespace itaxcix\Core\Domain\person;

use DateTime;
use itaxcix\Infrastructure\Database\Entity\person\PersonEntity;

class PersonModel {
    private int $id;
    private ?string $name = null;
    private ?string $lastName = null;
    private ?DocumentTypeModel $documentType = null;
    private string $document;
    private ?DateTime $validationDate;
    private ?string $image = null;
    private bool $active = true;

    public function __construct(
        int $id,
        ?string $name,
        ?string $lastName,
        ?DocumentTypeModel $documentType,
        string $document,
        ?DateTime $validationDate,
        ?string $image = null,
        bool $active = true
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->documentType = $documentType;
        $this->document = $document;
        $this->validationDate = $validationDate;
        $this->image = $image;
        $this->active = $active;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getDocumentType(): ?DocumentTypeModel
    {
        return $this->documentType;
    }

    public function setDocumentType(?DocumentTypeModel $documentType): void
    {
        $this->documentType = $documentType;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function setDocument(string $document): void
    {
        $this->document = $document;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getValidationDate(): ?DateTime
    {
        return $this->validationDate;
    }

    public function setValidationDate(?DateTime $validationDate): void
    {
        $this->validationDate = $validationDate;
    }

    public function toEntity(): PersonEntity {
        $entity = new PersonEntity();

        $entity->setId($this->getId());
        $entity->setName($this->getName());
        $entity->setLastName($this->getLastName());
        $entity->setDocumentType($this->getDocumentType()?->toEntity());
        $entity->setDocument($this->getDocument());
        $entity->setValidationDate($this->getValidationDate());
        $entity->setImage($this->getImage());
        $entity->setActive($this->isActive());

        return $entity;
    }
}