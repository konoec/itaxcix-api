<?php

namespace itaxcix\Core\Domain\vehicle;

use DateTime;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucProcedureEntity;

class TucProcedureModel {
    private ?int $id;
    private ?VehicleModel $vehicle = null;
    private ?CompanyModel $company = null;
    private ?DistrictModel $district = null;
    private ?TucStatusModel $status = null;
    private ?ProcedureTypeModel $type = null;
    private ?TucModalityModel $modality = null;
    private ?DateTime $procedureDate = null;
    private ?DateTime $issueDate = null;
    private ?DateTime $expirationDate = null;

    /**
     * @param ?int $id
     * @param VehicleModel|null $vehicle
     * @param CompanyModel|null $company
     * @param DistrictModel|null $district
     * @param TucStatusModel|null $status
     * @param ProcedureTypeModel|null $type
     * @param TucModalityModel|null $modality
     * @param DateTime|null $procedureDate
     * @param DateTime|null $issueDate
     * @param DateTime|null $expirationDate
     */
    public function __construct(?int $id, ?VehicleModel $vehicle, ?CompanyModel $company, ?DistrictModel $district, ?TucStatusModel $status, ?ProcedureTypeModel $type, ?TucModalityModel $modality, ?DateTime $procedureDate, ?DateTime $issueDate, ?DateTime $expirationDate)
    {
        $this->id = $id;
        $this->vehicle = $vehicle;
        $this->company = $company;
        $this->district = $district;
        $this->status = $status;
        $this->type = $type;
        $this->modality = $modality;
        $this->procedureDate = $procedureDate;
        $this->issueDate = $issueDate;
        $this->expirationDate = $expirationDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getVehicle(): ?VehicleModel
    {
        return $this->vehicle;
    }

    public function setVehicle(?VehicleModel $vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    public function getCompany(): ?CompanyModel
    {
        return $this->company;
    }

    public function setCompany(?CompanyModel $company): void
    {
        $this->company = $company;
    }

    public function getDistrict(): ?DistrictModel
    {
        return $this->district;
    }

    public function setDistrict(?DistrictModel $district): void
    {
        $this->district = $district;
    }

    public function getStatus(): ?TucStatusModel
    {
        return $this->status;
    }

    public function setStatus(?TucStatusModel $status): void
    {
        $this->status = $status;
    }

    public function getType(): ?ProcedureTypeModel
    {
        return $this->type;
    }

    public function setType(?ProcedureTypeModel $type): void
    {
        $this->type = $type;
    }

    public function getModality(): ?TucModalityModel
    {
        return $this->modality;
    }

    public function setModality(?TucModalityModel $modality): void
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

    public function toEntity(): TucProcedureEntity
    {
        $tucProcedureEntity = new TucProcedureEntity();
        $tucProcedureEntity->setId($this->id);
        $tucProcedureEntity->setVehicle($this->vehicle?->toEntity());
        $tucProcedureEntity->setCompany($this->company?->toEntity());
        $tucProcedureEntity->setDistrict($this->district?->toEntity());
        $tucProcedureEntity->setStatus($this->status?->toEntity());
        $tucProcedureEntity->setType($this->type?->toEntity());
        $tucProcedureEntity->setModality($this->modality?->toEntity());
        $tucProcedureEntity->setProcedureDate($this->procedureDate);
        $tucProcedureEntity->setIssueDate($this->issueDate);
        $tucProcedureEntity->setExpirationDate($this->expirationDate);

        return $tucProcedureEntity;
    }
}