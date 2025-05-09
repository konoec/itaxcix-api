<?php

namespace itaxcix\models\dtos;

use Exception;
use itaxcix\utils\ValidationHelper;
use itaxcix\validators\AliasValidator;
use itaxcix\validators\PasswordValidator;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "LoginRequest", description: "Datos para iniciar sesión")]
class LoginRequest {
    #[OA\Property(property: "username", type: "string", example: "juan.perez")]
    public readonly string $username;
    #[OA\Property(property: "password", type: "string", format: "password", example: "securePassword123")]
    public readonly string $password;

    /**
     * Constructor de la clase LoginRequest.
     *
     * @param array $data Datos del request.
     * @throws Exception Si los datos no son válidos.
     */
    public function __construct(array $data) {
        $this->username = $data['username'] ?? throw new Exception("El campo 'username' es requerido.", 400);
        $this->password = $data['password'] ?? throw new Exception("El campo 'password' es requerido.", 400);

        ValidationHelper::handle(fn() => AliasValidator::validate($this->username));
        ValidationHelper::handle(fn() => PasswordValidator::validate($this->password));
    }
}