<?php

namespace itaxcix\Shared\DTO\useCases\Category;

class CategoryRequestDTO
{
    public ?int $id;
    public ?string $name;
    public bool $active;

    public function __construct(?int $id, ?string $name, bool $active = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            active: isset($data['active']) ? (bool) $data['active'] : true
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active
        ];
    }
}
