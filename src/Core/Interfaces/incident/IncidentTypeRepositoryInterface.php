<?php

namespace itaxcix\Core\Interfaces\incident;

use itaxcix\Core\Domain\incident\IncidentTypeModel;
use itaxcix\Infrastructure\Database\Entity\incident\IncidentTypeEntity;

interface IncidentTypeRepositoryInterface {
    public function findIncidentTypeByName(string $name): ?IncidentTypeModel;
}
