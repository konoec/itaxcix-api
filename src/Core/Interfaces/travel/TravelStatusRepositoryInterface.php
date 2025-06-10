<?php

namespace itaxcix\Core\Interfaces\travel;

use itaxcix\Core\Domain\travel\TravelStatusModel;

interface TravelStatusRepositoryInterface
{
    public function findTravelStatusByName(string $name): ?TravelStatusModel;
}