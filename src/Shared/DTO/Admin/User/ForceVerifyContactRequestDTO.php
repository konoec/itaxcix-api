<?php

namespace itaxcix\Shared\DTO\Admin\User;

class ForceVerifyContactRequestDTO
{
    public readonly int $userId;
    public readonly int $contactId;

    public function __construct(
        int $userId,
        int $contactId
    ) {
        $this->userId = $userId;
        $this->contactId = $contactId;
    }
}
