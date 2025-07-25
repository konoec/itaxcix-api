<?php

namespace itaxcix\Core\Interfaces\user;

use itaxcix\Core\Domain\user\UserContactModel;

interface UserContactRepositoryInterface
{
    public function findAllUserContactByValue(string $value): ?UserContactModel;
    public function findUserContactByTypeAndUser(int $typeId, int $userId): ?UserContactModel;
    public function saveUserContact(UserContactModel $contact): UserContactModel;
    public function findActiveContactByUserAndType(int $userId, int $contactTypeId): ?UserContactModel;
    public function findConfirmedContactByUserAndType(int $userId, int $contactTypeId): ?UserContactModel;
    public function deleteContact(int $contactId): void;
    public function findUserContactByUserId(int $userId): ?UserContactModel;
    // Métodos necesarios para administración avanzada
    public function findUserContactById(int $contactId): ?UserContactModel;
    public function findAllUserContactByUserId(int $userId): array;
    public function updateContactConfirmation(int $contactId, bool $confirmed): bool;
    public function findContactsByUserAndType(int $userId, int $typeId): array;
    public function toDomain(object $entity): UserContactModel;
    public function findByContactTypeId(int $contactTypeId): bool;
}
