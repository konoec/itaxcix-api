<?php

namespace itaxcix\Infrastructure\Web\Controller\api\HelpCenter;

use InvalidArgumentException;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterCreateUseCase;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterDeleteUseCase;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterListUseCase;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterPublicListUseCase;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterUpdateUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\HelpCenter\HelpCenterRequestDTO;
use itaxcix\Shared\Validators\useCases\HelpCenter\HelpCenterValidator;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HelpCenterController extends AbstractController
{
    private HelpCenterListUseCase $listUseCase;
    private HelpCenterPublicListUseCase $publicListUseCase;
    private HelpCenterCreateUseCase $createUseCase;
    private HelpCenterUpdateUseCase $updateUseCase;
    private HelpCenterDeleteUseCase $deleteUseCase;

    public function __construct(
        HelpCenterListUseCase $listUseCase,
        HelpCenterPublicListUseCase $publicListUseCase,
        HelpCenterCreateUseCase $createUseCase,
        HelpCenterUpdateUseCase $updateUseCase,
        HelpCenterDeleteUseCase $deleteUseCase
    ) {
        $this->listUseCase = $listUseCase;
        $this->publicListUseCase = $publicListUseCase;
        $this->createUseCase = $createUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
    }

    #[OA\Get(
        path: "/help-center",
        operationId: "getHelpCenterItems",
        description: "Obtiene elementos del centro de ayuda paginados para administración.",
        summary: "Lista elementos del centro de ayuda con paginación.",
        security: [["bearerAuth" => []]],
        tags: ["HelpCenter"]
    )]
    #[OA\Parameter(
        name: "page",
        description: "Número de página",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", default: 1, minimum: 1)
    )]
    #[OA\Parameter(
        name: "perPage",
        description: "Elementos por página",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", default: 10, minimum: 1, maximum: 100)
    )]
    #[OA\Response(
        response: 200,
        description: "Lista paginada de elementos del centro de ayuda",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/HelpCenterResponseDTO")),
                    new OA\Property(property: "meta", properties: [
                        new OA\Property(property: "total", type: "integer", example: 50),
                        new OA\Property(property: "perPage", type: "integer", example: 10),
                        new OA\Property(property: "currentPage", type: "integer", example: 1),
                        new OA\Property(property: "lastPage", type: "integer", example: 5)
                    ], type: "object")
                ], type: "object")
            ],
            type: "object"
        )
    )]
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $page = (int)($queryParams['page'] ?? 1);
        $perPage = (int)($queryParams['perPage'] ?? 10);

        // Validar límites
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        $dto = new HelpCenterPaginationRequestDTO($page, $perPage);
        $result = $this->listUseCase->execute($dto);

        return $this->ok($result);
    }

    #[OA\Get(
        path: "/help-center/public",
        operationId: "getPublicHelpCenterItems",
        description: "Obtiene elementos activos del centro de ayuda para la aplicación móvil.",
        summary: "Lista elementos activos del centro de ayuda.",
        security: [["bearerAuth" => []]],
        tags: ["HelpCenter"]
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de elementos activos del centro de ayuda",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/HelpCenterResponseDTO"))
            ],
            type: "object"
        )
    )]
    public function publicList(ServerRequestInterface $request): ResponseInterface
    {
        $helpItems = $this->publicListUseCase->execute();
        return $this->ok($helpItems);
    }

    #[OA\Post(
        path: "/help-center",
        operationId: "createHelpCenterItem",
        description: "Crea un nuevo elemento del centro de ayuda.",
        summary: "Crea elemento del centro de ayuda.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/HelpCenterRequestDTO")
        ),
        tags: ["HelpCenter"]
    )]
    #[OA\Response(
        response: 200,
        description: "Elemento creado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Elemento del centro de ayuda creado correctamente.")
            ],
            type: "object"
        )
    )]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $validator = new HelpCenterValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new HelpCenterRequestDTO(
                id: null,
                title: (string)$data['title'],
                subtitle: (string)$data['subtitle'],
                answer: (string)$data['answer'],
                active: $data['active'] ?? true
            );

            $result = $this->createUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Put(
        path: "/help-center/{id}",
        operationId: "updateHelpCenterItem",
        description: "Actualiza un elemento del centro de ayuda.",
        summary: "Actualiza elemento del centro de ayuda.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/HelpCenterRequestDTO")
        ),
        tags: ["HelpCenter"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID del elemento a actualizar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Elemento actualizado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Elemento del centro de ayuda actualizado correctamente.")
            ],
            type: "object"
        )
    )]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->getJsonBody($request);
            $id = (int)$request->getAttribute('id');

            $validator = new HelpCenterValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                return $this->validationError($errors);
            }

            $dto = new HelpCenterRequestDTO(
                id: $id,
                title: (string)$data['title'],
                subtitle: (string)$data['subtitle'],
                answer: (string)$data['answer'],
                active: $data['active'] ?? true
            );

            $result = $this->updateUseCase->execute($dto);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[OA\Delete(
        path: "/help-center/{id}",
        operationId: "deleteHelpCenterItem",
        description: "Elimina (desactiva) un elemento del centro de ayuda.",
        summary: "Elimina elemento del centro de ayuda.",
        security: [["bearerAuth" => []]],
        tags: ["HelpCenter"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID del elemento a eliminar",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Elemento eliminado correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Elemento del centro de ayuda eliminado correctamente.")
            ],
            type: "object"
        )
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = (int)$request->getAttribute('id');
            $result = $this->deleteUseCase->execute($id);
            return $this->ok($result);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
