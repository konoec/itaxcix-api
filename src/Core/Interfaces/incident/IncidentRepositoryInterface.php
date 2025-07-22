<?php

namespace itaxcix\Core\Interfaces\incident;

use itaxcix\Core\Domain\incident\IncidentModel;
use itaxcix\Core\Domain\incident\IncidentTypeModel;
use itaxcix\Shared\DTO\useCases\Incident\GetUserIncidentsRequestDTO;
use itaxcix\Shared\DTO\useCases\IncidentReport\IncidentReportRequestDTO;

interface IncidentRepositoryInterface {
    public function saveIncident(IncidentModel $incidentModel): IncidentModel;

    // Métodos para reporte administrativo de incidentes
    public function findReport(IncidentReportRequestDTO $dto): array;
    public function countReport(IncidentReportRequestDTO $dto): int;

    // Método para obtener incidentes por usuario
    public function findByUser(GetUserIncidentsRequestDTO $dto): array;
    public function countByUser(GetUserIncidentsRequestDTO $dto): int;
    public function findActivesByIncidentTypeId(int $incidentTypeId): array;
}
