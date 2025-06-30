<?php

namespace itaxcix\Shared\DTO\useCases\UserReport;

class UserReportRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20,
        public readonly ?string $name = null,
        public readonly ?string $lastName = null,
        public readonly ?string $document = null,
        public readonly ?int $documentTypeId = null,
        public readonly ?int $statusId = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?bool $active = null,
        public readonly ?string $validationStartDate = null,
        public readonly ?string $validationEndDate = null,
        public readonly ?string $sortBy = 'name',
        public readonly ?string $sortDirection = 'ASC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 20))),
            name: $data['name'] ?? null,
            lastName: $data['lastName'] ?? null,
            document: $data['document'] ?? null,
            documentTypeId: isset($data['documentTypeId']) ? (int)$data['documentTypeId'] : null,
            statusId: isset($data['statusId']) ? (int)$data['statusId'] : null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            active: isset($data['active']) ? filter_var($data['active'], FILTER_VALIDATE_BOOLEAN) : null,
            validationStartDate: $data['validationStartDate'] ?? null,
            validationEndDate: $data['validationEndDate'] ?? null,
            sortBy: $data['sortBy'] ?? 'name',
            sortDirection: strtoupper($data['sortDirection'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC'
        );
    }
}

