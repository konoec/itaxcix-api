<?php

namespace itaxcix\Shared\DTO\Admin\User;

class AdminUserDetailResponseDTO
{
    public readonly int $userId;
    public readonly array $person;
    public readonly array $userStatus;
    public readonly array $contacts;
    public readonly array $roles;
    public readonly ?array $citizenProfile;
    public readonly ?array $driverProfile;
    public readonly ?array $vehicle;

    public function __construct(
        int $userId,
        array $person,
        array $userStatus,
        array $contacts,
        array $roles,
        ?array $citizenProfile = null,
        ?array $driverProfile = null,
        ?array $vehicle = null
    ) {
        $this->userId = $userId;
        $this->person = $person;
        $this->userStatus = $userStatus;
        $this->contacts = $contacts;
        $this->roles = $roles;
        $this->citizenProfile = $citizenProfile;
        $this->driverProfile = $driverProfile;
        $this->vehicle = $vehicle;
    }
}
