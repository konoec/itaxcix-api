<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\ServiceTypeModel;

interface ServiceTypeRepositoryInterface
{
    public function findAllServiceTypeByName(string $name): ?ServiceTypeModel;
    public function saveServiceType(ServiceTypeModel $serviceTypeModel): ServiceTypeModel;
}