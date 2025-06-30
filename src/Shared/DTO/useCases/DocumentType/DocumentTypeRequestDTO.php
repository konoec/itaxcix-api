<?php

namespace itaxcix\Shared\DTO\useCases\DocumentType;

class DocumentTypeRequestDTO
{
    private string $name;
    private ?bool $active;

    public function __construct(string $name, ?bool $active = null)
    {
        $this->name = $name;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }
}
