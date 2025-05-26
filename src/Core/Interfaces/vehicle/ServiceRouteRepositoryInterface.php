<?php

namespace itaxcix\Core\Interfaces\vehicle;

use itaxcix\Core\Domain\vehicle\ServiceRouteModel;

interface ServiceRouteRepositoryInterface
{
    public function saveServiceRoute(ServiceRouteModel $serviceRouteModel): ServiceRouteModel;
}