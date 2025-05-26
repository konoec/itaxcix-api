<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\TucProcedureModel;

interface TucProcedureRepositoryInterface
{
    public function saveTucProcedure(TucProcedureModel $tucProcedureModel): TucProcedureModel;
}