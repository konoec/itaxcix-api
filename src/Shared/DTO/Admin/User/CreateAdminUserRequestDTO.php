<?php

namespace itaxcix\Shared\DTO\Admin\User;

class CreateAdminUserRequestDTO
{
    public readonly string $document;
    public readonly string $email;
    public readonly string $password;
    public readonly string $area;
    public readonly string $position;

    public function __construct(
        string $document,
        string $email,
        string $password,
        string $area,
        string $position
    ) {
        $this->document = $document;
        $this->email = $email;
        $this->password = $password;
        $this->area = $area;
        $this->position = $position;
    }
}
