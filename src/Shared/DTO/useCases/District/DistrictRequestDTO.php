<?php

namespace itaxcix\Shared\DTO\useCases\District;

class DistrictRequestDTO
{
    private ?int $id;
    private string $name;
    private int $provinceId;
    private ?string $ubigeo;

    public function __construct(?int $id, string $name, int $provinceId, ?string $ubigeo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->provinceId = $provinceId;
        $this->ubigeo = $ubigeo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProvinceId(): int
    {
        return $this->provinceId;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: trim($data['name'] ?? ''),
            provinceId: (int)($data['provinceId'] ?? 0),
            ubigeo: isset($data['ubigeo']) ? trim($data['ubigeo']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'provinceId' => $this->provinceId,
            'ubigeo' => $this->ubigeo
        ];
    }
}
