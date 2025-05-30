<?php

namespace itaxcix\Infrastructure\Database\Entity\user;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tb_codigo_usuario')]
class UserCodeEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codi_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserCodeTypeEntity::class)]
    #[ORM\JoinColumn(name: 'codi_tipo_id', referencedColumnName: 'tipo_id')]
    private ?UserCodeTypeEntity $type = null;
    #[ORM\ManyToOne(targetEntity: UserContactEntity::class)]
    #[ORM\JoinColumn(name: 'codi_contacto_id', referencedColumnName: 'cont_id')]
    private ?UserContactEntity $contact = null;
    #[ORM\Column(name: 'codi_codigo', type: 'string', length: 8)]
    private string $code;
    #[ORM\Column(name: 'codi_fecha_expiracion', type: 'datetime')]
    private DateTime $expirationDate;
    #[ORM\Column(name: 'codi_fecha_uso', type: 'datetime', nullable: true)]
    private ?DateTime $useDate = null;
    #[ORM\Column(name: 'codi_usado', type: 'boolean', options: ['default' => false])]
    private bool $used = false;

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

    public function getType(): ?UserCodeTypeEntity
    {
        return $this->type;
    }

    public function setType(?UserCodeTypeEntity $type): void
    {
        $this->type = $type;
    }

    public function getContact(): ?UserContactEntity
    {
        return $this->contact;
    }

    public function setContact(?UserContactEntity $contact): void
    {
        $this->contact = $contact;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getExpirationDate(): DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(DateTime $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function getUseDate(): ?DateTime
    {
        return $this->useDate;
    }

    public function setUseDate(?DateTime $useDate): void
    {
        $this->useDate = $useDate;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): void
    {
        $this->used = $used;
    }
}