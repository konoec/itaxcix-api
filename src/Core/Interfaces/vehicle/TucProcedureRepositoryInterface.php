<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\TucProcedureModel;

interface TucProcedureRepositoryInterface
{
    public function findTucProceduresByVehicleIdAndStatusId(int $vehicleId, int $statusId): ?array;
    public function findTucProcedureByVehicleId(int $vehicleId): ?TucProcedureModel;
    public function findAllTucProceduresByVehicleId(int $vehicleId): array;
    public function saveTucProcedure(TucProcedureModel $tucProcedureModel): TucProcedureModel;
    public function findTucProcedureWithMaxExpirationDateByVehicleId(int $vehicleId): ?TucProcedureModel;
    public function findByCompanyId(int $companyId): array;
    public function findByDistrictId(int $districtId): array;
    public function findByTucModalityId(int $tucModalityId): array;
    public function findByTucStatusId(int $tucStatusId): array;
    public function findByProcedureTypeId(int $procedureTypeId): array;
}