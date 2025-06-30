<?php

namespace itaxcix\Shared\DTO\useCases\Company;

class CompanyRequestDTO
{
    private string $ruc;
    private ?string $name;
    private bool $active;

    public function __construct(string $ruc, ?string $name, bool $active = true)
    {
        $this->ruc = $ruc;
        $this->name = $name;
        $this->active = $active;
    }

    public function getRuc(): string
    {
        return $this->ruc;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
