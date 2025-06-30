<?php

namespace itaxcix\Shared\DTO\useCases\ContactType;

class ContactTypeResponseDTO
{
    public int $id;
    public string $name;
    public bool $active;

    public function __construct(int $id, string $name, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }
}
