<?php

namespace itaxcix\models\dtos;

use Exception;
use itaxcix\validators\PasswordValidator;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "ResetPasswordRequest", description: "Datos para restablecer la contraseña")]
class ResetPasswordRequest {

    #[OA\Property(property: "userId", type: "integer", example: 1)]
    public readonly int $userId;
    #[OA\Property(property: "newPassword", type: "string", format: "password", example: "secureNewPassword123")]
    public readonly string $newPassword;

    /**
     * Constructor de la clase ResetPasswordRequest.
     *
     * @param array $data Datos del request.
     * @throws Exception Si los datos no son válidos.
     */
    public function __construct(array $data) {
        $this->userId = $data['userId'] ?? throw new Exception("El campo 'userId' es requerido.", 400);
        $this->newPassword = $data['newPassword'] ?? throw new Exception("El campo 'newPassword' es requerido.", 400);

        PasswordValidator::validate($this->newPassword);
    }
}