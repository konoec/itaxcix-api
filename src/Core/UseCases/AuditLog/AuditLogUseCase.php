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

        // Usar output buffering para capturar la salida de fputcsv
        ob_start();
        $output = fopen('php://output', 'w');

        // Escribir encabezados
        fputcsv($output, ['ID', 'Tabla', 'Operación', 'Usuario', 'Fecha', 'Dato Anterior', 'Dato Nuevo']);

        // Escribir datos
        foreach ($logs as $log) {
            // Procesar datos JSON para que sean seguros en CSV
            $previousData = $log['previousData'] ? json_encode($log['previousData'], JSON_UNESCAPED_UNICODE) : 'null';
            $newData = $log['newData'] ? json_encode($log['newData'], JSON_UNESCAPED_UNICODE) : 'null';

            // Limpiar saltos de línea problemáticos en JSON
            $previousData = str_replace(["\r", "\n"], ['', ''], $previousData);
            $newData = str_replace(["\r", "\n"], ['', ''], $newData);

            fputcsv($output, [
                $log['id'],
                $log['affectedTable'],
                $log['operation'],
                $log['systemUser'],
                $log['date'],
                $previousData,
                $newData
            ]);
        }

        fclose($output);
        $csv = ob_get_clean();

        return $csv;
    }
}

