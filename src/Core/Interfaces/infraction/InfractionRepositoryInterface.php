<?php

namespace itaxcix\Core\Interfaces\infraction;

use itaxcix\Shared\DTO\useCases\InfractionReport\InfractionReportRequestDTO;

interface InfractionRepositoryInterface
{
    public function findReport(InfractionReportRequestDTO $dto): array;
    public function countReport(InfractionReportRequestDTO $dto): int;
    public function findActivesBySeverityId(int $severityId): array;
    public function findActivesByStatusId(int $statusId): array;
}
