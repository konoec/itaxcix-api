<?php

namespace itaxcix\Shared\DTO\Admin\User;

class CreateAdminUserRequestDTO
{
    public readonly string $firstName;
    public readonly string $lastName;
    public readonly string $document;
    public readonly int $documentTypeId;
    public readonly string $email;
    public readonly string $password;
    public readonly string $area;
    public readonly string $position;

    public function __construct(
        string $firstName,
        string $lastName,
        string $document,
        int $documentTypeId,
        string $email,
        string $password,
        string $area,
        string $position
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->document = $document;
        $this->documentTypeId = $documentTypeId;
        $this->email = $email;
        $this->password = $password;
        $this->area = $area;
        $this->position = $position;
    }
}
