<?php

namespace itaxcix\Core\Domain\location;

class CoordinatesModel {
    private ?int $id = null;
    private string $name;
    private ?DistrictModel $district = null;
    private string $latitude;
    private string $longitude;
}