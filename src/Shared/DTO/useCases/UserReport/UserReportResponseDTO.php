<?php

namespace itaxcix\Shared\DTO\useCases\UserReport;

class UserReportResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $lastName,
        public readonly string $document,
        public readonly string $documentType,
        public readonly string $status,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly bool $active,
        public readonly ?string $validationDate
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastName' => $this->lastName,
            'document' => $this->document,
            'documentType' => $this->documentType,
            'status' => $this->status,
            'email' => $this->email,
            'phone' => $this->phone,
            'active' => $this->active,
            'validationDate' => $this->validationDate
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            lastName: $data['lastName'],
            document: $data['document'],
            documentType: $data['documentType'],
            status: $data['status'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            active: $data['active'],
            validationDate: $data['validationDate'] ?? null
        );
    }
}

