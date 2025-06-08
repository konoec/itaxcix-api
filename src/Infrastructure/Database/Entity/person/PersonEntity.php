<?php

namespace itaxcix\Infrastructure\Database\Entity\person;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Repository\person\DoctrinePersonRepository;

#[ORM\Entity(repositoryClass: DoctrinePersonRepository::class)]
#[ORM\Table(name: 'tb_persona')]
class PersonEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'pers_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'pers_nombre', type: 'string', length: 100, nullable: false)]
    private ?string $name = null;
    #[ORM\Column(name: 'pers_apellido', type: 'string', length: 100, nullable: false)]
    private ?string $lastName = null;
    #[ORM\ManyToOne(targetEntity: DocumentTypeEntity::class)]
    #[ORM\JoinColumn(name: 'pers_tipo_documento_id', referencedColumnName: 'tipo_id', nullable: false)]
    private ?DocumentTypeEntity $documentType = null;
    #[ORM\Column(name: 'pers_documento', type: 'string', length: 20, unique: true, nullable: false)]
    private string $document;
    #[ORM\Column(name: 'pers_fecha_validacion', type: 'datetime', nullable: true)]
    private ?DateTime $validationDate;
    #[ORM\Column(name: 'pers_imagen', type: 'string', length: 255, nullable: true)]
    private ?string $image;
    #[ORM\Column(name: 'pers_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;

    public function __construct() {
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

    public function getDocumentType(): ?DocumentTypeEntity
    {
        return $this->documentType;
    }

    public function setDocumentType(?DocumentTypeEntity $documentType): void
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


}