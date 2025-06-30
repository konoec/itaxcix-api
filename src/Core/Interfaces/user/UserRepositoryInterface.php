<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Shared\DTO\useCases\UserReport\UserReportRequestDTO;

interface UserRepositoryInterface {
    public function findUserByPersonDocument(string $document): ?UserModel;
    public function findAllUserByPersonDocument(string $document): ?UserModel;
    public function findAllUserByPersonId(int $personId): ?UserModel;
    public function findUserById(int $id): ?UserModel;
    public function saveUser(UserModel $userModel): UserModel;

    // Nuevos métodos para administración avanzada
    public function findById(int $id): ?UserModel;
    public function findAllPaginated(
        int $page = 1,
        int $limit = 20,
        ?string $search = null,
        ?int $roleId = null,
        ?int $statusId = null,
        ?bool $withWebAccess = null
    ): array;
    public function countAll(
        ?string $search = null,
        ?int $roleId = null,
        ?int $statusId = null,
        ?bool $withWebAccess = null
    ): int;

    // Método específico para filtros administrativos avanzados
    public function findUsersPaginatedWithFilters(
        int $page,
        int $limit,
        array $filters
    ): array;

    // Métodos para reporte administrativo de usuarios
    public function findReport(UserReportRequestDTO $dto): array;
    public function countReport(UserReportRequestDTO $dto): int;
}