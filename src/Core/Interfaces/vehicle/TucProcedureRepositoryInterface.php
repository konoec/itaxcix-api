<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\TucProcedureModel;

interface TucProcedureRepositoryInterface
{
    public function findTucProcedureByVehicleId(int $vehicleId): ?TucProcedureModel;
    public function saveTucProcedure(TucProcedureModel $tucProcedureModel): TucProcedureModel;
}