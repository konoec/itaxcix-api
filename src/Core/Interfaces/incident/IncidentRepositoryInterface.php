<?php

namespace itaxcix\Core\Interfaces\incident;

use itaxcix\Core\Domain\incident\IncidentModel;
use itaxcix\Core\Domain\incident\IncidentTypeModel;

interface IncidentRepositoryInterface {
    public function saveIncident(IncidentModel $incidentModel): IncidentModel;

    // Métodos para reporte administrativo de incidentes
    public function findReport(\itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO $dto): array;
    public function countReport(\itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO $dto): int;
}
