<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\TucStatusModel;

interface TucStatusRepositoryInterface
{
    public function findAllTucStatusByName(string $name): ?TucStatusModel;
    public function saveTucStatus(TucStatusModel $tucStatusModel): TucStatusModel;
}