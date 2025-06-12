<?php

namespace itaxcix\Core\Interfaces\incident;

use itaxcix\Core\Domain\incident\IncidentModel;
use itaxcix\Core\Domain\incident\IncidentTypeModel;

interface IncidentRepositoryInterface {
    public function saveIncident(IncidentModel $incidentModel): IncidentModel;
}

