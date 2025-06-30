<?php

namespace itaxcix\Shared\DTO\Admin\User;

class ChangeUserStatusRequestDTO
{
    public readonly int $userId;
    public readonly int $statusId;
    public readonly ?string $reason;

    public function __construct(
        int $userId,
        int $statusId,
        ?string $reason = null
    ) {
        $this->userId = $userId;
        $this->statusId = $statusId;
        $this->reason = $reason;
    }
}
