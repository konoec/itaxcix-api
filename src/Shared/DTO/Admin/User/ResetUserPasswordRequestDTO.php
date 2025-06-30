<?php

namespace itaxcix\Shared\DTO\Admin\User;

class ResetUserPasswordRequestDTO
{
    public readonly int $userId;
    public readonly string $newPassword;
    public readonly bool $forcePasswordChange;
    public readonly ?string $adminReason;

    public function __construct(
        int $userId,
        string $newPassword,
        bool $forcePasswordChange = true,
        ?string $adminReason = null
    ) {
        $this->userId = $userId;
        $this->newPassword = $newPassword;
        $this->forcePasswordChange = $forcePasswordChange;
        $this->adminReason = $adminReason;
    }
}
