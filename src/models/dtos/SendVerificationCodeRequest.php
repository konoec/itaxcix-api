<?php

namespace itaxcix\models\dtos;

use itaxcix\validators\ContactTypeValidator;
use itaxcix\validators\ContactValidator;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "SendVerificationCodeRequest", description: "Datos necesarios para enviar un código de verificación")]
class SendVerificationCodeRequest {
    #[OA\Property(property: "contactTypeId", type: "integer", example: 1)]
    public int $contactTypeId;

    #[OA\Property(property: "contact", type: "string", example: "usuario@ejemplo.com")]
    public string $contact;

    public function __construct(array $data) {
        $this->contactTypeId = filter_var($data['contactTypeId'] ?? null, FILTER_VALIDATE_INT);
        $this->contact = filter_var($data['contact'] ?? '', FILTER_SANITIZE_EMAIL);

        ContactTypeValidator::validate($this->contactTypeId);
        ContactValidator::validate($this->contact, $this->contactTypeId);
    }
}