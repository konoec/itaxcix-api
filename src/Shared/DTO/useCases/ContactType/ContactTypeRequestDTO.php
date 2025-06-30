<?php

namespace itaxcix\Shared\DTO\useCases\ContactType;

class ContactTypeRequestDTO
{
    public string $name;
    public bool $active;

    public function __construct(string $name, bool $active = true)
    {
        $this->name = $name;
        $this->active = $active;
    }
}
