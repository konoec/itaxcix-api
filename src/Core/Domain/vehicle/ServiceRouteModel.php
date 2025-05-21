<?php

namespace itaxcix\Core\Domain\vehicle;

class ServiceRouteModel {
    private int $id;
    private ?TucProcedureModel $procedure = null;
    private ?ServiceTypeModel $serviceType = null;
    private ?string $text = null;
    private bool $active = true;
}