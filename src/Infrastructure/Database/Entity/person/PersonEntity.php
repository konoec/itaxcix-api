<?php

namespace itaxcix\Infrastructure\Database\Entity\person;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
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
    #[ORM\Column(name: 'pers_validado', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $validated = true;
    #[ORM\Column(name: 'pers_imagen', type: 'string', length: 255, nullable: true)]
    private ?string $image = null;
    #[ORM\Column(name: 'pers_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;
}