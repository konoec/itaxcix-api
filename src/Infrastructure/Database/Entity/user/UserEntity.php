<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\person\PersonEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_usuario')]
class UserEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'usua_id', type: 'integer')]
    private int $id;
    #[ORM\Column(name: 'usua_alias', type: 'string', length: 50, unique: true, nullable: false)]
    private string $alias;
    #[ORM\Column(name: 'usua_clave', type: 'string', length: 255, nullable: false)]
    private string $password;
    #[ORM\OneToOne(targetEntity: PersonEntity::class)]
    #[ORM\JoinColumn(name: 'usua_persona_id', referencedColumnName: 'pers_id', nullable: false)]
    private ?PersonEntity $person = null;
    #[ORM\ManyToOne(targetEntity: UserStatusEntity::class)]
    #[ORM\JoinColumn(name: 'usua_estado_id', referencedColumnName: 'esta_id', nullable: false)]
    private ?UserStatusEntity $status = null;
    #[ORM\OneToMany(
        targetEntity: UserContactEntity::class,
        mappedBy: 'user',
        orphanRemoval: true
    )]
    private Collection $contacts;

    public function __construct() {
        $this->contacts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPerson(): ?PersonEntity
    {
        return $this->person;
    }

    public function setPerson(?PersonEntity $person): void
    {
        $this->person = $person;
    }

    public function getStatus(): ?UserStatusEntity
    {
        return $this->status;
    }

    public function setStatus(?UserStatusEntity $status): void
    {
        $this->status = $status;
    }

    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function setContacts(Collection $contacts): void
    {
        $this->contacts = $contacts;
    }
}