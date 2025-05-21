<?php

namespace itaxcix\Core\Domain\vehicle;

class TechnicalSpecificationModel {
    private int $id;
    private VehicleModel $vehicle;
    private ?float $dryWeight = null;
    private ?float $grossWeight = null;
    private ?float $length = null;
    private ?float $height = null;
    private ?float $width = null;
    private ?float $payloadCapacity = null;
}