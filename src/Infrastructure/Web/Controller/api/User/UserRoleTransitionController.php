<?php

namespace itaxcix\Infrastructure\Web\Controller\api\User;

use Exception;
use InvalidArgumentException;
use itaxcix\Core\UseCases\User\CitizenToDriverUseCase;
use itaxcix\Core\UseCases\User\DriverToCitizenUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\Validators\useCases\User\CitizenToDriverValidator;
use itaxcix\Shared\Validators\useCases\User\DriverToCitizenValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserRoleTransitionController extends AbstractController
{
    private CitizenToDriverUseCase $citizenToDriverUseCase;
    private DriverToCitizenUseCase $driverToCitizenUseCase;
    private CitizenToDriverValidator $citizenToDriverValidator;
    private DriverToCitizenValidator $driverToCitizenValidator;

    public function __construct(
        CitizenToDriverUseCase $citizenToDriverUseCase,
        DriverToCitizenUseCase $driverToCitizenUseCase,
        CitizenToDriverValidator $citizenToDriverValidator,
        DriverToCitizenValidator $driverToCitizenValidator
    ) {
        $this->citizenToDriverUseCase = $citizenToDriverUseCase;
        $this->driverToCitizenUseCase = $driverToCitizenUseCase;
        $this->citizenToDriverValidator = $citizenToDriverValidator;
        $this->driverToCitizenValidator = $driverToCitizenValidator;
    }

    #[OA\Post(
        path: "/users/request-driver-role",
        operationId: "requestDriverRole",
        description: "Permite a un ciudadano solicitar convertirse en conductor asociando un vehículo. La solicitud quedará pendiente de aprobación del administrador.",
        summary: "Solicitar rol de conductor",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "userId", type: "integer", example: 123, description: "ID del usuario ciudadano"),
                    new OA\Property(property: "vehicleId", type: "integer", example: 456, description: "ID del vehículo a asociar")
                ],
                type: "object"
            )
        ),
        tags: ["User Role Transitions"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Solicitud enviada correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "userId", type: "integer", example: 123),
                            new OA\Property(property: "status", type: "string", example: "PENDIENTE"),
                            new OA\Property(property: "message", type: "string", example: "Solicitud para ser conductor enviada correctamente. Esperando aprobación del administrador."),
                            new OA\Property(property: "driverProfileId", type: "integer", example: 789)
                        ], type: "object")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: "Error de validación",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "El vehículo ya está asignado a otro usuario."),
                        new OA\Property(property: "errors", type: "array", items: new OA\Items(type: "string"))
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 401,
                description: "No autorizado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Token de acceso inválido")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function transitionToDriver(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            // Validar datos
            $errors = $this->citizenToDriverValidator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Crear DTO y ejecutar caso de uso
            $dto = $this->citizenToDriverValidator->createDTO($data);
            $result = $this->citizenToDriverUseCase->execute($dto);

            return $this->ok([
                'userId' => $result->getUserId(),
                'status' => $result->getStatus(),
                'message' => $result->getMessage(),
                'driverProfileId' => $result->getDriverProfileId()
            ]);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (Exception $e) {
            return $this->error('Error interno del servidor', 500);
        }
    }

    #[OA\Post(
        path: "/users/request-citizen-role",
        operationId: "requestCitizenRole",
        description: "Permite a un conductor obtener también el rol de ciudadano. El perfil se crea inmediatamente sin necesidad de aprobación.",
        summary: "Obtener rol de ciudadano",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "userId", description: "ID del usuario conductor", type: "integer", example: 123)
                ],
                type: "object"
            )
        ),
        tags: ["User Role Transitions"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Perfil de ciudadano creado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "userId", type: "integer", example: 123),
                            new OA\Property(property: "status", type: "string", example: "ACTIVO"),
                            new OA\Property(property: "message", type: "string", example: "Perfil de ciudadano creado correctamente. Ya puedes usar ambos roles."),
                            new OA\Property(property: "citizenProfileId", type: "integer", example: 789)
                        ], type: "object")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: "Error de validación",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "El usuario ya tiene un perfil de ciudadano."),
                        new OA\Property(property: "errors", type: "array", items: new OA\Items(type: "string"))
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 401,
                description: "No autorizado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Token de acceso inválido")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function transitionToCitizen(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);

            // Validar datos
            $errors = $this->driverToCitizenValidator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            // Crear DTO y ejecutar caso de uso
            $dto = $this->driverToCitizenValidator->createDTO($data);
            $result = $this->driverToCitizenUseCase->execute($dto);

            return $this->ok([
                'userId' => $result->getUserId(),
                'status' => $result->getStatus(),
                'message' => $result->getMessage(),
                'citizenProfileId' => $result->getCitizenProfileId()
            ]);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (Exception $e) {
            return $this->error('Error interno del servidor', 500);
        }
    }
}
