<?php

namespace itaxcix\Infrastructure\Database\Entity\travel;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\location\CoordinatesEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_viaje')]
class TravelEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'viaj_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_usuario_ciudadano_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $citizen = null;
    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_usuario_conductor_id', referencedColumnName: 'usua_id')]
    private ?UserEntity $driver = null;
    #[ORM\ManyToOne(targetEntity: CoordinatesEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_origen_id', referencedColumnName: 'coor_id')]
    private ?CoordinatesEntity $origin = null;
    #[ORM\ManyToOne(targetEntity: CoordinatesEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_destino_id', referencedColumnName: 'coor_id')]
    private ?CoordinatesEntity $destination = null;
    #[ORM\Column(name: 'viaj_fecha_inicio', type: 'datetime', nullable: true)]
    private ?DateTime $startDate = null;
    #[ORM\Column(name: 'viaj_fecha_fin', type: 'datetime', nullable: true)]
    private ?DateTime $endDate = null;
    #[ORM\Column(name: 'viaj_fecha_creacion', type: 'datetime')]
    private DateTime $creationDate;
    #[ORM\ManyToOne(targetEntity: TravelStatusEntity::class)]
    #[ORM\JoinColumn(name: 'viaj_estado_id', referencedColumnName: 'esta_id')]
    private TravelStatusEntity $status;

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

    public function getCitizen(): ?UserEntity
    {
        return $this->citizen;
    }

    public function setCitizen(?UserEntity $citizen): void
    {
        $this->citizen = $citizen;
    }

    public function getDriver(): ?UserEntity
    {
        return $this->driver;
    }

    public function setDriver(?UserEntity $driver): void
    {
        $this->driver = $driver;
    }

    public function getOrigin(): ?CoordinatesEntity
    {
        return $this->origin;
    }

    public function setOrigin(?CoordinatesEntity $origin): void
    {
        $this->origin = $origin;
    }

    public function getDestination(): ?CoordinatesEntity
    {
        return $this->destination;
    }

    public function setDestination(?CoordinatesEntity $destination): void
    {
        $this->destination = $destination;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creationDate;
    }

    public function setCreationDate(DateTime $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    public function getStatus(): TravelStatusEntity
    {
        return $this->status;
    }

    public function setStatus(TravelStatusEntity $status): void
    {
        $this->status = $status;
    }

}