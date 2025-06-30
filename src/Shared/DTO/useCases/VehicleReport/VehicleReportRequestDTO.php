<?php

namespace itaxcix\Shared\DTO\useCases\VehicleReport;

class VehicleReportRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?string $licensePlate = null,
        public readonly ?int $brandId = null,
        public readonly ?int $modelId = null,
        public readonly ?int $colorId = null,
        public readonly ?int $manufactureYearFrom = null,
        public readonly ?int $manufactureYearTo = null,
        public readonly ?int $seatCount = null,
        public readonly ?int $passengerCount = null,
        public readonly ?int $fuelTypeId = null,
        public readonly ?int $vehicleClassId = null,
        public readonly ?int $categoryId = null,
        public readonly ?bool $active = null,
        public readonly ?int $companyId = null,
        public readonly ?int $districtId = null,
        public readonly ?int $statusId = null,
        public readonly ?int $procedureTypeId = null,
        public readonly ?int $modalityId = null,
        public readonly ?string $sortBy = 'licensePlate',
        public readonly ?string $sortDirection = 'ASC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 20))),
            licensePlate: $data['licensePlate'] ?? null,
            brandId: isset($data['brandId']) ? (int)$data['brandId'] : null,
            modelId: isset($data['modelId']) ? (int)$data['modelId'] : null,
            colorId: isset($data['colorId']) ? (int)$data['colorId'] : null,
            manufactureYearFrom: isset($data['manufactureYearFrom']) ? (int)$data['manufactureYearFrom'] : null,
            manufactureYearTo: isset($data['manufactureYearTo']) ? (int)$data['manufactureYearTo'] : null,
            seatCount: isset($data['seatCount']) ? (int)$data['seatCount'] : null,
            passengerCount: isset($data['passengerCount']) ? (int)$data['passengerCount'] : null,
            fuelTypeId: isset($data['fuelTypeId']) ? (int)$data['fuelTypeId'] : null,
            vehicleClassId: isset($data['vehicleClassId']) ? (int)$data['vehicleClassId'] : null,
            categoryId: isset($data['categoryId']) ? (int)$data['categoryId'] : null,
            active: isset($data['active']) ? filter_var($data['active'], FILTER_VALIDATE_BOOLEAN) : null,
            companyId: isset($data['companyId']) ? (int)$data['companyId'] : null,
            districtId: isset($data['districtId']) ? (int)$data['districtId'] : null,
            statusId: isset($data['statusId']) ? (int)$data['statusId'] : null,
            procedureTypeId: isset($data['procedureTypeId']) ? (int)$data['procedureTypeId'] : null,
            modalityId: isset($data['modalityId']) ? (int)$data['modalityId'] : null,
            sortBy: $data['sortBy'] ?? 'licensePlate',
            sortDirection: strtoupper($data['sortDirection'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC'
        );
    }
}

