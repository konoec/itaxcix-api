<?php

namespace itaxcix\Shared\DTO\Admin\User;

class ForceVerifyContactRequestDTO
{
    public readonly int $userId;
    public readonly int $contactId;
    public readonly string $adminReason;

    public function __construct(
        int $userId,
        int $contactId,
        string $adminReason
    ) {
        $this->userId = $userId;
        $this->contactId = $contactId;
        $this->adminReason = $adminReason;
    }
}
