<?php

namespace itaxcix\Shared\DTO\useCases\Company;

class CompanyResponseDTO
{
    private ?int $id;
    private string $ruc;
    private ?string $name;
    private bool $active;

    public function __construct(?int $id, string $ruc, ?string $name, bool $active)
    {
        $this->id = $id;
        $this->ruc = $ruc;
        $this->name = $name;
        $this->active = $active;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ruc' => $this->ruc,
            'name' => $this->name,
            'active' => $this->active
        ];
    }
}
