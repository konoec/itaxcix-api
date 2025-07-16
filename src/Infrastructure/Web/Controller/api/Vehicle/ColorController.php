<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Handler\Color\ColorCreateUseCaseHandler;
use itaxcix\Core\Handler\Color\ColorDeleteUseCaseHandler;
use itaxcix\Core\Handler\Color\ColorListUseCaseHandler;
use itaxcix\Core\Handler\Color\ColorUpdateUseCaseHandler;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Color\ColorPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Color\ColorRequestDTO;
use itaxcix\Shared\Validators\useCases\Color\ColorValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ColorController extends AbstractController
{
    private ColorListUseCaseHandler $listHandler;
    private ColorCreateUseCaseHandler $createHandler;
    private ColorUpdateUseCaseHandler $updateHandler;
    private ColorDeleteUseCaseHandler $deleteHandler;
    private ColorValidator $validator;

    public function __construct(
        ColorListUseCaseHandler $listHandler,
        ColorCreateUseCaseHandler $createHandler,
        ColorUpdateUseCaseHandler $updateHandler,
        ColorDeleteUseCaseHandler $deleteHandler,
        ColorValidator $validator
    ) {
        $this->listHandler = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->validator = $validator;
    }

    #[OA\Get(
        path: "/admin/colors",
        operationId: "getColors",
        description: "Obtiene colores con búsqueda, filtros y paginación avanzada para panel administrativo.",
        summary: "Lista colores con funcionalidades administrativas.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Color"]
    )]
    #[OA\Parameter(name: "page", description: "Número de página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1, minimum: 1))]
    #[OA\Parameter(name: "perPage", description: "Elementos por página", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100))]
    #[OA\Parameter(name: "search", description: "Búsqueda global en nombre", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "name", description: "Filtro por nombre del color", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "active", description: "Filtro por estado activo", in: "query", required: false, schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "sortBy", description: "Campo de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "name", "active"], default: "name"))]
    #[OA\Parameter(name: "sortDirection", description: "Dirección de ordenamiento", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["ASC", "DESC"], default: "ASC"))]
    #[OA\Response(
        response: 200,
        description: "Lista de colores con paginación",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Colores obtenidos exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Azul"),
                                    new OA\Property(property: "active", type: "boolean", example: true)
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "pagination",
                            type: "object",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "total_items", type: "integer", example: 25),
                                new OA\Property(property: "total_pages", type: "integer", example: 2),
                                new OA\Property(property: "has_next_page", type: "boolean", example: true),
                                new OA\Property(property: "has_previous_page", type: "boolean", example: false)
                            ]
                        )
                    ]
                )
            ]
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $paginationRequest = ColorPaginationRequestDTO::fromArray($queryParams);

            $result = $this->listHandler->handle($paginationRequest);

            return $this->ok($result);
        } catch (\Exception $e) {
            return $this->error('Error al obtener colores: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/admin/colors",
        operationId: "createColor",
        description: "Crea un nuevo color en el sistema.",
        summary: "Crear nuevo color.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Color"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Verde", description: "Nombre del color"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del color")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Color creado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Color creado exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Verde"),
                        new OA\Property(property: "active", type: "boolean", example: true)
                    ]
                )
            ]
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = $this->getJsonBody($request);
            $colorRequest = ColorRequestDTO::fromArray($body);

            $errors = $this->validator->validate($colorRequest);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $result = $this->createHandler->handle($colorRequest);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->error('Error al crear color: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Put(
        path: "/admin/colors/{id}",
        operationId: "updateColor",
        description: "Actualiza un color existente.",
        summary: "Actualizar color.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Color"]
    )]
    #[OA\Parameter(name: "id", description: "ID del color", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Azul Actualizado", description: "Nombre del color"),
                new OA\Property(property: "active", type: "boolean", example: true, description: "Estado activo del color")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Color actualizado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Color actualizado exitosamente"),
                new OA\Property(
                    property: "data",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Azul Actualizado"),
                        new OA\Property(property: "active", type: "boolean", example: true)
                    ]
                )
            ]
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');
            $body = $this->getJsonBody($request);
            $colorRequest = ColorRequestDTO::fromArray($body);

            $errors = $this->validator->validateForUpdate($colorRequest, $id);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $result = $this->updateHandler->handle($id, $colorRequest);

            return $this->ok($result->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar color: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/admin/colors/{id}",
        operationId: "deleteColor",
        description: "Elimina un color del sistema.",
        summary: "Eliminar color.",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Color"]
    )]
    #[OA\Parameter(name: "id", description: "ID del color", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Color eliminado exitosamente",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Color eliminado exitosamente"),
                new OA\Property(property: "data", type: "object", example: null)
            ]
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int) $request->getAttribute('id');

            $result = $this->deleteHandler->handle($id);

            if (!$result) {
                return $this->error('No se pudo eliminar el color', 500);
            }

            return $this->ok([
                'message' => 'Color eliminado exitosamente'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error('Error al eliminar color: ' . $e->getMessage(), 500);
        }
    }
}
