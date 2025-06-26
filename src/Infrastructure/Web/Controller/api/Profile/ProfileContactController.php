<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Profile;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Profile\ChangeEmailUseCase;
use itaxcix\Core\UseCases\Profile\ChangePhoneUseCase;
use itaxcix\Core\UseCases\Profile\VerifyEmailChangeUseCase;
use itaxcix\Core\UseCases\Profile\VerifyPhoneChangeUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Profile\ChangeEmailRequestDTO;
use itaxcix\Shared\DTO\useCases\Profile\ChangePhoneRequestDTO;
use itaxcix\Shared\DTO\useCases\Profile\VerifyEmailChangeRequestDTO;
use itaxcix\Shared\DTO\useCases\Profile\VerifyPhoneChangeRequestDTO;
use itaxcix\Shared\Validators\useCases\Profile\ChangeEmailRequestValidator;
use itaxcix\Shared\Validators\useCases\Profile\ChangePhoneRequestValidator;
use itaxcix\Shared\Validators\useCases\Profile\VerifyEmailChangeRequestValidator;
use itaxcix\Shared\Validators\useCases\Profile\VerifyPhoneChangeRequestValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProfileContactController extends AbstractController
{
    private ChangeEmailUseCase $changeEmailUseCase;
    private ChangePhoneUseCase $changePhoneUseCase;
    private VerifyEmailChangeUseCase $verifyEmailChangeUseCase;
    private VerifyPhoneChangeUseCase $verifyPhoneChangeUseCase;

    public function __construct(
        ChangeEmailUseCase $changeEmailUseCase,
        ChangePhoneUseCase $changePhoneUseCase,
        VerifyEmailChangeUseCase $verifyEmailChangeUseCase,
        VerifyPhoneChangeUseCase $verifyPhoneChangeUseCase
    ) {
        $this->changeEmailUseCase = $changeEmailUseCase;
        $this->changePhoneUseCase = $changePhoneUseCase;
        $this->verifyEmailChangeUseCase = $verifyEmailChangeUseCase;
        $this->verifyPhoneChangeUseCase = $verifyPhoneChangeUseCase;
    }

    #[OA\Post(
        path: "/profile/change-email",
        operationId: "requestEmailChange",
        description: "Solicita el cambio de correo electrónico enviando un código de verificación.",
        summary: "Solicitar cambio de correo",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: ChangeEmailRequestDTO::class)
        ),
        tags: ["Profile"]
    )]
    #[OA\Response(
        response: 200,
        description: "Código enviado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "message", type: "string", example: "Se ha enviado un código de verificación a tu nuevo correo")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Errores de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "El correo electrónico es inválido")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function changeEmail(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            // Validar que el userId del body existe
            if (!isset($data['userId'])) {
                return $this->error('userId es requerido', 400);
            }

            // Obtener y validar que el user_id del token coincida con el userId del body
            $payload = $request->getAttribute('user');
            if (!$payload || !isset($payload['user_id']) || $payload['user_id'] !== $data['userId']) {
                return $this->error('No autorizado para modificar este usuario', 401);
            }

            $validator = new ChangeEmailRequestValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new ChangeEmailRequestDTO(
                userId: (int) $data['userId'], // Convertir a int ya que viene como string del JSON
                email: $data['email']
            );

            $result = $this->changeEmailUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Post(
        path: "/profile/verify-email",
        operationId: "verifyEmailChange",
        description: "Verifica el código enviado para cambiar el correo electrónico.",
        summary: "Verificar cambio de correo",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: VerifyEmailChangeRequestDTO::class)
        ),
        tags: ["Profile"]
    )]
    #[OA\Response(
        response: 200,
        description: "Correo actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "message", type: "string", example: "Correo electrónico actualizado correctamente")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function verifyEmail(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            // Validar que el userId del body existe
            if (!isset($data['userId'])) {
                return $this->error('userId es requerido', 400);
            }

            // Obtener y validar que el user_id del token coincida con el userId del body
            $payload = $request->getAttribute('user');
            if (!$payload || !isset($payload['user_id']) || $payload['user_id'] !== $data['userId']) {
                return $this->error('No autorizado para modificar este usuario', 401);
            }

            $validator = new VerifyEmailChangeRequestValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new VerifyEmailChangeRequestDTO(
                userId: (int) $data['userId'],
                code: $data['code']
            );

            $result = $this->verifyEmailChangeUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Post(
        path: "/profile/change-phone",
        operationId: "requestPhoneChange",
        description: "Solicita el cambio de número telefónico enviando un código de verificación.",
        summary: "Solicitar cambio de teléfono",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: ChangePhoneRequestDTO::class)
        ),
        tags: ["Profile"]
    )]
    #[OA\Response(
        response: 200,
        description: "Código enviado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "message", type: "string", example: "Se ha enviado un código de verificación a tu nuevo número")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Errores de validación",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: false),
                new OA\Property(property: "message", type: "string", example: "Petición inválida"),
                new OA\Property(property: "error", properties: [
                    new OA\Property(property: "message", type: "string", example: "El número telefónico es inválido")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function changePhone(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            // Validar que el userId del body existe
            if (!isset($data['userId'])) {
                return $this->error('userId es requerido', 400);
            }

            // Obtener y validar que el user_id del token coincida con el userId del body
            $payload = $request->getAttribute('user');
            if (!$payload || !isset($payload['user_id']) || $payload['user_id'] !== $data['userId']) {
                return $this->error('No autorizado para modificar este usuario', 401);
            }

            $validator = new ChangePhoneRequestValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new ChangePhoneRequestDTO(
                userId: (int) $data['userId'],
                phone: $data['phone']
            );

            $result = $this->changePhoneUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Post(
        path: "/profile/verify-phone",
        operationId: "verifyPhoneChange",
        description: "Verifica el código enviado para cambiar el número telefónico.",
        summary: "Verificar cambio de teléfono",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: VerifyPhoneChangeRequestDTO::class)
        ),
        tags: ["Profile"]
    )]
    #[OA\Response(
        response: 200,
        description: "Teléfono actualizado exitosamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "message", type: "string", example: "Número telefónico actualizado correctamente")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function verifyPhone(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            // Validar que el userId del body existe
            if (!isset($data['userId'])) {
                return $this->error('userId es requerido', 400);
            }

            // Obtener y validar que el user_id del token coincida con el userId del body
            $payload = $request->getAttribute('user');
            if (!$payload || !isset($payload['user_id']) || $payload['user_id'] !== $data['userId']) {
                return $this->error('No autorizado para modificar este usuario', 401);
            }

            $validator = new VerifyPhoneChangeRequestValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new VerifyPhoneChangeRequestDTO(
                userId: (int) $data['userId'],
                code: $data['code']
            );

            $result = $this->verifyPhoneChangeUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
