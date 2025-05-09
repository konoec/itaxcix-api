<?php

namespace itaxcix\models\dtos;

use itaxcix\validators\ContactTypeValidator;
use itaxcix\validators\ContactValidator;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "RecoveryRequest", description: "Datos para recuperar contraseña")]
class RecoveryRequest {
    #[OA\Property(property: "contactTypeId", type: "integer", example: 1)]
    public readonly int $contactTypeId;
    #[OA\Property(property: "contact", type: "string", example: "antonio@gmail.com")]
    public readonly string $contact;

    /**
     * Constructor de la clase RecoveryRequest.
     *
     * @param array $data Datos del request.
     * @throws \Exception Si los datos no son válidos.
     */
    public function __construct(array $data) {
        $this->contactTypeId = $data['contactTypeId'] ?? throw new \Exception("El campo 'contactTypeId' es requerido.", 400);
        $this->contact = $data['contact'] ?? throw new \Exception("El campo 'contact' es requerido.", 400);

        ContactTypeValidator::validate($this->contactTypeId);
        ContactValidator::validate($this->contact, $this->contactTypeId);
    }
}