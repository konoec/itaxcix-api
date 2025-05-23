<?php

namespace itaxcix\Infrastructure\Database\Entity\infraction;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_infraccion')]
class InfractionEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'infr_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'infr_usuario_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $user = null;
    #[ORM\ManyToOne(targetEntity: InfractionSeverityEntity::class)]
    #[ORM\JoinColumn(name: 'infr_gravedad_id', referencedColumnName: 'grav_id')]
    private ?InfractionSeverityEntity $severity = null;
    #[ORM\Column(name: 'infr_fecha', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $date;
    #[ORM\Column(name: 'infr_descripcion', type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\ManyToOne(targetEntity: InfractionStatusEntity::class)]
    #[ORM\JoinColumn(name: 'infr_estado_id', referencedColumnName: 'esta_id')]
    private ?InfractionStatusEntity $status = null;

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

    public function getSeverity(): ?InfractionSeverityEntity
    {
        return $this->severity;
    }

    public function setSeverity(?InfractionSeverityEntity $severity): void
    {
        $this->severity = $severity;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): ?InfractionStatusEntity
    {
        return $this->status;
    }

    public function setStatus(?InfractionStatusEntity $status): void
    {
        $this->status = $status;
    }
}