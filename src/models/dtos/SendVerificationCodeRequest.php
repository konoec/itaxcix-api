<?php

namespace itaxcix\models\dtos;

class SendVerificationCodeRequest
{
    public function __construct(array $data)
    {
        $this->contactTypeId = filter_var($data['contactTypeId'] ?? null, FILTER_VALIDATE_INT);
        $this->contact = filter_var($data['contact'] ?? '', FILTER_SANITIZE_EMAIL);
    }

    public int $contactTypeId;
    public string $contact;
}