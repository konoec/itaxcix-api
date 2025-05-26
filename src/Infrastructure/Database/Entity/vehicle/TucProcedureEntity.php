<?php

namespace itaxcix\Infrastructure\Database\Entity\vehicle;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use itaxcix\Infrastructure\Database\Entity\company\CompanyEntity;
use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;

#[ORM\Entity]
#[ORM\Table(name: 'tb_tramite_tuc')]
class TucProcedureEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tram_id', type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: VehicleEntity::class)]
    #[ORM\JoinColumn(name: 'tram_vehiculo_id', referencedColumnName: 'vehi_id')]
    private ?VehicleEntity $vehicle = null;
    #[ORM\ManyToOne(targetEntity: CompanyEntity::class)]
    #[ORM\JoinColumn(name: 'tram_empresa_id', referencedColumnName: 'empr_id')]
    private ?CompanyEntity $company = null;
    #[ORM\ManyToOne(targetEntity: DistrictEntity::class)]
    #[ORM\JoinColumn(name: 'tram_distrito_id', referencedColumnName: 'dist_id')]
    private ?DistrictEntity $district = null;
    #[ORM\ManyToOne(targetEntity: TucStatusEntity::class)]
    #[ORM\JoinColumn(name: 'tram_estado_id', referencedColumnName: 'esta_id')]
    private ?TucStatusEntity $status = null;
    #[ORM\ManyToOne(targetEntity: ProcedureTypeEntity::class)]
    #[ORM\JoinColumn(name: 'tram_tipo_id', referencedColumnName: 'tipo_id')]
    private ?ProcedureTypeEntity $type = null;
    #[ORM\ManyToOne(targetEntity: TucModalityEntity::class)]
    #[ORM\JoinColumn(name: 'tram_modalidad_id', referencedColumnName: 'moda_id')]
    private ?TucModalityEntity $modality = null;
    #[ORM\Column(name: 'tram_fecha_tramite', type: 'date', nullable: true)]
    private ?DateTime $procedureDate = null;
    #[ORM\Column(name: 'tram_fecha_emision', type: 'date', nullable: true)]
    private ?DateTime $issueDate = null;
    #[ORM\Column(name: 'tram_fecha_caducidad', type: 'date', nullable: true)]
    private ?DateTime $expirationDate = null;

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

    public function getVehicle(): ?VehicleEntity
    {
        return $this->vehicle;
    }

    public function setVehicle(?VehicleEntity $vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    public function getCompany(): ?CompanyEntity
    {
        return $this->company;
    }

    public function setCompany(?CompanyEntity $company): void
    {
        $this->company = $company;
    }

    public function getDistrict(): ?DistrictEntity
    {
        return $this->district;
    }

    public function setDistrict(?DistrictEntity $district): void
    {
        $this->district = $district;
    }

    public function getStatus(): ?TucStatusEntity
    {
        return $this->status;
    }

    public function setStatus(?TucStatusEntity $status): void
    {
        $this->status = $status;
    }

    public function getType(): ?ProcedureTypeEntity
    {
        return $this->type;
    }

    public function setType(?ProcedureTypeEntity $type): void
    {
        $this->type = $type;
    }

    public function getModality(): ?TucModalityEntity
    {
        return $this->modality;
    }

    public function setModality(?TucModalityEntity $modality): void
    {
        $this->modality = $modality;
    }

    public function getProcedureDate(): ?DateTime
    {
        return $this->procedureDate;
    }

    public function setProcedureDate(?DateTime $procedureDate): void
    {
        $this->procedureDate = $procedureDate;
    }

    public function getIssueDate(): ?DateTime
    {
        return $this->issueDate;
    }

    public function setIssueDate(?DateTime $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    public function getExpirationDate(): ?DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?DateTime $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

}