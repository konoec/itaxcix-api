<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\ProcedureTypeModel;

interface ProcedureTypeRepositoryInterface
{
    public function findAllProcedureTypeByName(string $name): ?ProcedureTypeModel;
    public function saveProcedureType(ProcedureTypeModel $procedureTypeModel): ProcedureTypeModel;
}