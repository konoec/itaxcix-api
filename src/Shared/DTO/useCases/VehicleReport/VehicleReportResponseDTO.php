<?php

namespace itaxcix\Shared\DTO\useCases\VehicleReport;

class VehicleReportResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $licensePlate,
        public readonly ?string $brandName,
        public readonly ?string $modelName,
        public readonly ?string $colorName,
        public readonly ?int $manufactureYear,
        public readonly ?int $seatCount,
        public readonly ?int $passengerCount,
        public readonly ?string $fuelTypeName,
        public readonly ?string $vehicleClassName,
        public readonly ?string $categoryName,
        public readonly bool $active,
        public readonly ?string $companyName,
        public readonly ?string $districtName,
        public readonly ?string $statusName,
        public readonly ?string $procedureTypeName,
        public readonly ?string $modalityName
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'licensePlate' => $this->licensePlate,
            'brandName' => $this->brandName,
            'modelName' => $this->modelName,
            'colorName' => $this->colorName,
            'manufactureYear' => $this->manufactureYear,
            'seatCount' => $this->seatCount,
            'passengerCount' => $this->passengerCount,
            'fuelTypeName' => $this->fuelTypeName,
            'vehicleClassName' => $this->vehicleClassName,
            'categoryName' => $this->categoryName,
            'active' => $this->active,
            'companyName' => $this->companyName,
            'districtName' => $this->districtName,
            'statusName' => $this->statusName,
            'procedureTypeName' => $this->procedureTypeName,
            'modalityName' => $this->modalityName
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            licensePlate: $data['licensePlate'],
            brandName: $data['brandName'] ?? null,
            modelName: $data['modelName'] ?? null,
            colorName: $data['colorName'] ?? null,
            manufactureYear: $data['manufactureYear'] ?? null,
            seatCount: $data['seatCount'] ?? null,
            passengerCount: $data['passengerCount'] ?? null,
            fuelTypeName: $data['fuelTypeName'] ?? null,
            vehicleClassName: $data['vehicleClassName'] ?? null,
            categoryName: $data['categoryName'] ?? null,
            active: $data['active'],
            companyName: $data['companyName'] ?? null,
            districtName: $data['districtName'] ?? null,
            statusName: $data['statusName'] ?? null,
            procedureTypeName: $data['procedureTypeName'] ?? null,
            modalityName: $data['modalityName'] ?? null
        );
    }
}

