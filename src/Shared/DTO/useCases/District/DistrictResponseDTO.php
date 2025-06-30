<?php

namespace itaxcix\Shared\DTO\useCases\District;

use itaxcix\Core\Domain\location\DistrictModel;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "DistrictResponseDTO",
    description: "DTO de respuesta para distrito",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1, description: "ID del distrito"),
        new OA\Property(property: "name", type: "string", example: "Miraflores", description: "Nombre del distrito"),
        new OA\Property(property: "province", type: "object", description: "Provincia asociada", properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "name", type: "string", example: "Lima"),
            new OA\Property(property: "departmentId", type: "integer", example: 1),
            new OA\Property(property: "departmentName", type: "string", example: "Lima")
        ]),
        new OA\Property(property: "ubigeo", type: "string", example: "150122", nullable: true, description: "Código ubigeo del distrito")
    ]
)]
class DistrictResponseDTO
{
    private int $id;
    private string $name;
    private array $province;
    private ?string $ubigeo;

    public function __construct(int $id, string $name, array $province, ?string $ubigeo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->province = $province;
        $this->ubigeo = $ubigeo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProvince(): array
    {
        return $this->province;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public static function fromModel(DistrictModel $model): self
    {
        return new self(
            id: $model->getId(),
            name: $model->getName(),
            province: [
                'id' => $model->getProvince()?->getId(),
                'name' => $model->getProvince()?->getName(),
                'departmentId' => $model->getProvince()?->getDepartment()?->getId(),
                'departmentName' => $model->getProvince()?->getDepartment()?->getName()
            ],
            ubigeo: $model->getUbigeo()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'province' => $this->province,
            'ubigeo' => $this->ubigeo
        ];
    }
}
