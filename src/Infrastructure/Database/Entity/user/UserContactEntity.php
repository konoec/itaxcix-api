<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_contacto_usuario')]
class UserContactEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cont_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(name: 'cont_usuario_id', referencedColumnName: 'usua_id', nullable: false)]
    private ?UserEntity $user;
    #[ORM\ManyToOne(targetEntity: ContactTypeEntity::class)]
    #[ORM\JoinColumn(name: 'cont_tipo_id', referencedColumnName: 'tipo_id', nullable: false)]
    private ?ContactTypeEntity $type;
    #[ORM\Column(name: 'cont_valor', type: 'string', length: 100, unique: true, nullable: false)]
    private string $value;
    #[ORM\Column(name: 'cont_confirmado', type: 'boolean', nullable: false)]
    private bool $confirmed;
    #[ORM\Column(name: 'cont_activo', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $active = true;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    public function getType(): ?ContactTypeEntity
    {
        return $this->type;
    }

    public function setType(?ContactTypeEntity $type): void
    {
        $this->type = $type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

}