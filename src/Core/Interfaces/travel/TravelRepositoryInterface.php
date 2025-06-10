<?php

namespace itaxcix\Core\Interfaces\travel;

use itaxcix\Core\Domain\travel\TravelModel;

interface TravelRepositoryInterface
{
    public function saveTravel(TravelModel $travelModel): TravelModel;
    public function findTravelById(int $id): ?TravelModel;
}