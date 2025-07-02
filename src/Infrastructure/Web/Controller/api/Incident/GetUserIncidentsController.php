<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Incident;

use InvalidArgumentException;
use itaxcix\Core\UseCases\Incident\GetUserIncidentsUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Incident\GetUserIncidentsRequestDTO;
use itaxcix\Shared\DTO\useCases\Incident\GetUserIncidentsResponseDTO;
use itaxcix\Shared\Validators\useCases\Incident\GetUserIncidentsValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[OA\Get(
    path: "/incidents/user/{userId}",
    operationId: "getUserIncidents",
    description: "Obtiene los incidentes de un usuario específico con filtros y paginación.",
    summary: "Obtener incidentes por usuario",
    security: [["bearerAuth" => []]],
    tags: ["Incident"]
)]
#[OA\Parameter(
    name: "userId",
    description: "ID del usuario",
    in: "path",
    required: true,
    schema: new OA\Schema(type: "integer", example: 1)
)]
#[OA\Parameter(
    name: "travelId",
    description: "ID del viaje (filtro opcional)",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "integer", example: 123)
)]
#[OA\Parameter(
    name: "typeId",
    description: "ID del tipo de incidencia (filtro opcional)",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "integer", example: 1)
)]
#[OA\Parameter(
    name: "active",
    description: "Estado activo del incidente (filtro opcional)",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "boolean", example: true)
)]
#[OA\Parameter(
    name: "comment",
    description: "Búsqueda en comentarios (filtro opcional)",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "string", example: "problema")
)]
#[OA\Parameter(
    name: "page",
    description: "Número de página",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "integer", example: 1, minimum: 1)
)]
#[OA\Parameter(
    name: "perPage",
    description: "Elementos por página",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "integer", example: 10, minimum: 1, maximum: 100)
)]
#[OA\Parameter(
    name: "sortBy",
    description: "Campo de ordenamiento",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "string", example: "id", enum: ["id", "travelId", "typeId", "active"])
)]
#[OA\Parameter(
    name: "sortDirection",
    description: "Dirección del ordenamiento",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "string", example: "DESC", enum: ["ASC", "DESC"])
)]
#[OA\Response(
    response: 200,
    description: "Incidentes obtenidos correctamente",
    content: new OA\JsonContent(ref: GetUserIncidentsResponseDTO::class)
)]
#[OA\Response(
    response: 400,
    description: "Errores de validación",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "success", type: "boolean", example: false),
            new OA\Property(property: "message", type: "string", example: "Errores de validación"),
            new OA\Property(property: "errors", type: "object", example: ["userId" => "El ID del usuario es requerido"])
        ],
        type: "object"
    )
)]
class GetUserIncidentsController extends AbstractController
{
    private GetUserIncidentsUseCase $getUserIncidentsUseCase;

    public function __construct(GetUserIncidentsUseCase $getUserIncidentsUseCase)
    {
        $this->getUserIncidentsUseCase = $getUserIncidentsUseCase;
    }

    public function getUserIncidents(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $userId = $request->getAttribute('userId');

            // Preparar datos para validación
            $data = array_merge($queryParams, ['userId' => (int)$userId]);

            // Convertir valores de query params
            if (isset($data['travelId'])) {
                $data['travelId'] = (int)$data['travelId'];
            }
            if (isset($data['typeId'])) {
                $data['typeId'] = (int)$data['typeId'];
            }
            if (isset($data['active'])) {
                $data['active'] = filter_var($data['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
            if (isset($data['page'])) {
                $data['page'] = (int)$data['page'];
            }
            if (isset($data['perPage'])) {
                $data['perPage'] = (int)$data['perPage'];
            }

            $validator = new GetUserIncidentsValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new GetUserIncidentsRequestDTO(
                userId: $data['userId'],
                travelId: $data['travelId'] ?? null,
                typeId: $data['typeId'] ?? null,
                active: $data['active'] ?? null,
                comment: $data['comment'] ?? null,
                page: $data['page'] ?? 1,
                perPage: $data['perPage'] ?? 10,
                sortBy: $data['sortBy'] ?? 'id',
                sortDirection: $data['sortDirection'] ?? 'DESC'
            );

            $result = $this->getUserIncidentsUseCase->execute($dto);

            $responseDto = new GetUserIncidentsResponseDTO(
                incidents: $result['incidents'],
                pagination: $result['pagination']
            );

            return $this->ok($responseDto);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error interno del servidor', 500);
        }
    }
}
