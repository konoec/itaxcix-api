<?php

namespace itaxcix\Core\Interfaces\incident;

use itaxcix\Core\Domain\incident\IncidentModel;
use itaxcix\Core\Domain\incident\IncidentTypeModel;

interface IncidentRepositoryInterface {
    public function saveIncident(IncidentModel $incidentModel): IncidentModel;

    // Métodos para reporte administrativo de incidentes
    public function findReport(\itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO $dto): array;
    public function countReport(\itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO $dto): int;

    // Método para obtener incidentes por usuario
    public function findByUser(\itaxcix\Shared\DTO\useCases\Incident\GetUserIncidentsRequestDTO $dto): array;
    public function countByUser(\itaxcix\Shared\DTO\useCases\Incident\GetUserIncidentsRequestDTO $dto): int;
}
