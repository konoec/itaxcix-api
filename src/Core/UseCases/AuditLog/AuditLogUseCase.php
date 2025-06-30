<?php

namespace itaxcix\Core\UseCases\AuditLog;

use itaxcix\Core\Interfaces\audit\AuditLogRepositoryInterface;
use itaxcix\Shared\DTO\useCases\AuditLog\AuditLogRequestDTO;
use itaxcix\Shared\DTO\useCases\AuditLog\AuditLogPaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\AuditLog\AuditLogResponseDTO;

class AuditLogUseCase
{
    private AuditLogRepositoryInterface $repository;

    public function __construct(AuditLogRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AuditLogRequestDTO $dto): AuditLogPaginationResponseDTO
    {
        $result = $this->repository->findAuditLogs($dto);
        $total = $this->repository->countAuditLogs($dto);
        $totalPages = (int) ceil($total / $dto->perPage);
        return new AuditLogPaginationResponseDTO(
            data: array_map(fn($item) => $item instanceof AuditLogResponseDTO ? $item : AuditLogResponseDTO::fromArray($item), $result),
            currentPage: $dto->page,
            perPage: $dto->perPage,
            totalItems: $total,
            totalPages: $totalPages
        );
    }

    public function getDetails(int $id): ?AuditLogResponseDTO
    {
        $data = $this->repository->findAuditLogById($id);
        return $data ? AuditLogResponseDTO::fromArray($data) : null;
    }

    public function export(AuditLogRequestDTO $dto): string
    {
        $logs = $this->repository->findAuditLogs($dto, false); // sin paginación para exportar todo
        $csv = "ID,Tabla,Operación,Usuario,Fecha,Dato Anterior,Dato Nuevo\n";
        foreach ($logs as $log) {
            $csv .= sprintf(
                '"%d","%s","%s","%s","%s","%s","%s"\n',
                $log['id'],
                $log['affectedTable'],
                $log['operation'],
                $log['systemUser'],
                $log['date'],
                json_encode($log['previousData'], JSON_UNESCAPED_UNICODE),
                json_encode($log['newData'], JSON_UNESCAPED_UNICODE)
            );
        }
        return $csv;
    }
}

