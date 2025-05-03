<?php

namespace itaxcix\controllers;

use OpenApi\Attributes as OA;

#[OA\Tag(name: "Auth", description: "Operaciones relacionadas con autenticación de usuarios")]
class AuthController
{
    #[OA\Post(
        path: "/api/v1/auth/login",
        summary: "Iniciar sesión con alias y contraseña",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["alias", "password"],
                properties: [
                    new OA\Property(property: "alias", type: "string", example: "juanperez"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Inicio de sesión exitoso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.xxxxx")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Credenciales inválidas")
        ]
    )]
    public function login(): void {}

    #[OA\Post(
        path: "/api/v1/auth/register/citizen",
        summary: "Registrarse como ciudadano",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["documentType", "documentNumber", "alias", "password", "contactMethod"],
                properties: [
                    new OA\Property(property: "documentType", description: "tb_tipo_documento.tipo_id", type: "integer", example: 1),
                    new OA\Property(property: "documentNumber", description: "Número del documento", type: "string", example: "V12345678"),
                    new OA\Property(property: "alias", type: "string", example: "juanperez"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456"),
                    new OA\Property(property: "contactMethod", type: "object", example: ["email" => "juan@example.com"])
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Usuario registrado correctamente"
            ),
            new OA\Response(response: 400, description: "Datos inválidos o duplicados")
        ]
    )]
    public function registerCitizen(): void {}

    #[OA\Post(
        path: "/api/v1/auth/register/driver",
        summary: "Registrarse como conductor",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["documentType", "documentNumber", "alias", "password", "contactMethod", "licensePlate"],
                properties: [
                    new OA\Property(property: "documentType", type: "integer", example: 1),
                    new OA\Property(property: "documentNumber", type: "string", example: "V12345678"),
                    new OA\Property(property: "alias", type: "string", example: "carlosdriver"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456"),
                    new OA\Property(property: "contactMethod", type: "object", example: ["email" => "carlos@example.com"]),
                    new OA\Property(property: "licensePlate", type: "string", example: "A1B-234D")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Conductor registrado correctamente"
            ),
            new OA\Response(response: 400, description: "Datos inválidos o duplicados")
        ]
    )]
    public function registerDriver(): void {}

    #[OA\Post(
        path: "/api/v1/auth/recover/email",
        summary: "Solicitar recuperación de contraseña por correo",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Correo de recuperación enviado"
            ),
            new OA\Response(response: 404, description: "Usuario no encontrado")
        ]
    )]
    public function recoverByEmail(): void {}

    #[OA\Post(
        path: "/api/v1/auth/recover/phone",
        summary: "Solicitar recuperación de contraseña por teléfono",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["phone"],
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "+584121234567")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Código SMS enviado para recuperación"
            ),
            new OA\Response(response: 404, description: "Usuario no encontrado")
        ]
    )]
    public function recoverByPhone(): void {}

    #[OA\Post(
        path: "/api/v1/auth/verify-code",
        summary: "Verificar código de recuperación",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "code", type: "string", example: "123456"),
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+584121234567")
                ],
                oneOf: [
                    new OA\Schema(required: ["email", "code"]),
                    new OA\Schema(required: ["phone", "code"])
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Código verificado correctamente"
            ),
            new OA\Response(response: 400, description: "Código inválido o expirado")
        ]
    )]
    public function verifyCode(): void {}

    #[OA\Post(
        path: "/api/v1/auth/reset-password",
        summary: "Restablecer contraseña tras verificar código",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["userId", "newPassword"],
                properties: [
                    new OA\Property(property: "userId", type: "integer", example: 123),
                    new OA\Property(property: "newPassword", type: "string", format: "password", example: "nueva_contrasena_123")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Contraseña actualizada correctamente"
            ),
            new OA\Response(response: 400, description: "Datos inválidos o token expirado")
        ]
    )]
    public function resetPassword(): void {}
}