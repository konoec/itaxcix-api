<?php

namespace itaxcix\models\dtos;

use Exception;
use itaxcix\validators\AliasValidator;
use itaxcix\validators\ContactTypeValidator;
use itaxcix\validators\ContactValidator;
use itaxcix\validators\DocumentTypeValidator;
use itaxcix\validators\DocumentValidator;
use itaxcix\validators\LicensePlateValidator;
use itaxcix\validators\PasswordValidator;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "RegisterDriverRequest", description: "Datos para registrar un conductor")]
class RegisterDriverRequest {
    #[OA\Property(property: "documentTypeId", type: "integer", example: 1)]
    public readonly int $documentTypeId;
    #[OA\Property(property: "document", type: "string", example: "123456789")]
    public readonly string $document;
    #[OA\Property(property: "alias", type: "string", example: "antonio.perez")]
    public readonly string $alias;
    #[OA\Property(property: "password", type: "string", format: "password", example: "securePassword123")]
    public readonly string $password;
    #[OA\Property(property: "contactTypeId", type: "integer", example: 1)]
    public readonly int $contactTypeId;
    #[OA\Property(property: "contact", type: "string", example: "antonio@gmail.com")]
    public readonly string $contact;
    #[OA\Property(property: "licensePlate", type: "string", example: "ABC-123")]
    public readonly string $licensePlate;

    /**
     * Constructor de la clase RegisterDriverRequest.
     *
     * @param array $data Datos del request.
     * @throws Exception Si los datos no son válidos.
     */
    public function __construct(array $data) {
        $this->documentTypeId = $data['documentTypeId'] ?? throw new Exception("El campo 'documentTypeId' es requerido.", 400);
        $this->document = $data['document'] ?? throw new Exception("El campo 'document' es requerido.", 400);
        $this->alias = $data['alias'] ?? throw new Exception("El campo 'alias' es requerido.", 400);
        $this->password = $data['password'] ?? throw new Exception("El campo 'password' es requerido.", 400);
        $this->contactTypeId = $data['contactTypeId'] ?? throw new Exception("El campo 'contactTypeId' es requerido.", 400);
        $this->contact = $data['contact'] ?? throw new Exception("El campo 'contact' es requerido.", 400);
        $this->licensePlate = $data['licensePlate'] ?? throw new Exception("El campo 'licensePlate' es requerido.", 400);

        DocumentTypeValidator::validate($this->documentTypeId);
        DocumentValidator::validate($this->document, $this->documentTypeId);
        AliasValidator::validate($this->alias);
        PasswordValidator::validate($this->password);
        ContactTypeValidator::validate($this->contactTypeId);
        ContactValidator::validate($this->contact, $this->contactTypeId);
        LicensePlateValidator::validate($this->licensePlate);
    }
}
