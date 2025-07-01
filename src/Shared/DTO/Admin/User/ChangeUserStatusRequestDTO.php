<?php

namespace itaxcix\Shared\DTO\Admin\User;

readonly class ChangeUserStatusRequestDTO
{
    public int $userId;
    public int $statusId;

    public function __construct(
        int $userId,
        int $statusId
    ) {
        $this->userId = $userId;
        $this->statusId = $statusId;
    }
}
