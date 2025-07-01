<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserStatusModel;
use itaxcix\Shared\DTO\useCases\UserStatus\UserStatusPaginationRequestDTO;

interface UserStatusRepositoryInterface
{
    public function findUserStatusById(int $id): ?UserStatusModel;
    public function findUserStatusByName(string $name): ?UserStatusModel;
    public function findAll(UserStatusPaginationRequestDTO $request): array;
    public function findById(int $id): ?UserStatusModel;
    public function create(UserStatusModel $userStatus): UserStatusModel;
    public function update(UserStatusModel $userStatus): UserStatusModel;
    public function delete(int $id): bool;
    public function existsByName(string $name, ?int $excludeId = null): bool;
    public function count(UserStatusPaginationRequestDTO $request): int;
}
