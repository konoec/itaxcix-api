<?php

namespace itaxcix\models\dtos;

use Exception;
use itaxcix\validators\dtos\CodeValidator;
use itaxcix\validators\dtos\ContactTypeValidator;
use itaxcix\validators\dtos\ContactValidator;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "VerifyCodeRequest", description: "Datos para verificar el código de recuperación")]
class VerifyCodeRequest {
    #[OA\Property(property: "code", type: "string", example: "123456")]
    public readonly string $code;
    #[OA\Property(property: "contactTypeId", type: "integer", example: 1)]
    public readonly int $contactTypeId;
    #[OA\Property(property: "contact", type: "string", example: "antonio@gmail.com")]
    public readonly string $contact;

    /**
     * Constructor de la clase VerifyCodeRequest.
     *
     * @param array $data Datos del request.
     * @throws Exception Si los datos no son válidos.
     */
    public function __construct(array $data) {
        $this->code = $data['code'] ?? throw new Exception("El campo 'code' es requerido.", 400);
        $this->contactTypeId = $data['contactTypeId'] ?? throw new Exception("El campo 'contactTypeId' es requerido.", 400);
        $this->contact = $data['contact'] ?? throw new Exception("El campo 'contact' es requerido.", 400);

        CodeValidator::validate($this->code);
        ContactTypeValidator::validate($this->contactTypeId);
        ContactValidator::validate($this->contact, $this->contactTypeId);
    }
}