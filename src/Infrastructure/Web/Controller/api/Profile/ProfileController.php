<?php

namespace itaxcix\Infrastructure\Web\Controller\api\Profile;

use itaxcix\Core\UseCases\Profile\GetAdminProfileUseCase;
use itaxcix\Core\UseCases\Profile\GetCitizenProfileUseCase;
use itaxcix\Core\UseCases\Profile\GetDriverProfileUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\Profile\AdminProfileResponseDTO;
use itaxcix\Shared\DTO\useCases\Profile\CitizenProfileResponseDTO;
use itaxcix\Shared\DTO\useCases\Profile\DriverProfileResponseDTO;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[OA\Get(
    path: "/profile/admin/{userId}",
    operationId: "getAdminProfile",
    description: "Obtiene el perfil del administrador.",
    summary: "Perfil de administrador",
    tags: ["Profile"],
    parameters: [
        new OA\Parameter(
            name: "userId",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Perfil de administrador",
            content: new OA\JsonContent(ref: AdminProfileResponseDTO::class)
        )
    ]
)]
#[OA\Get(
    path: "/profile/citizen/{userId}",
    operationId: "getCitizenProfile",
    description: "Obtiene el perfil del ciudadano.",
    summary: "Perfil de ciudadano",
    tags: ["Profile"],
    parameters: [
        new OA\Parameter(
            name: "userId",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Perfil de ciudadano",
            content: new OA\JsonContent(ref: CitizenProfileResponseDTO::class)
        )
    ]
)]
#[OA\Get(
    path: "/profile/driver/{userId}",
    operationId: "getDriverProfile",
    description: "Obtiene el perfil del conductor.",
    summary: "Perfil de conductor",
    tags: ["Profile"],
    parameters: [
        new OA\Parameter(
            name: "userId",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Perfil de conductor",
            content: new OA\JsonContent(ref: DriverProfileResponseDTO::class)
        )
    ]
)]
class ProfileController extends AbstractController
{
    private GetAdminProfileUseCase $getAdminProfileUseCase;
    private GetCitizenProfileUseCase $getCitizenProfileUseCase;
    private GetDriverProfileUseCase $getDriverProfileUseCase;

    public function __construct(
        GetAdminProfileUseCase $getAdminProfileUseCase,
        GetCitizenProfileUseCase $getCitizenProfileUseCase,
        GetDriverProfileUseCase $getDriverProfileUseCase
    ) {
        $this->getAdminProfileUseCase = $getAdminProfileUseCase;
        $this->getCitizenProfileUseCase = $getCitizenProfileUseCase;
        $this->getDriverProfileUseCase = $getDriverProfileUseCase;
    }

    public function getAdminProfile(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Obtener datos
        $data = ['userId' => $request->getAttribute('userId')];

        // 2. Validar datos
        if (!isset($data['userId'])) {
            return $this->error('El ID de usuario es requerido y debe ser un número entero.', 400);
        }

        // 3. Ejecutar caso de uso
        $userId = (int)$data['userId'];

        try {
            $profile = $this->getAdminProfileUseCase->execute($userId);
            return $this->ok($profile);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function getCitizenProfile(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Obtener datos
        $data = ['userId' => $request->getAttribute('userId')];

        // 2. Validar datos
        if (!isset($data['userId'])) {
            return $this->error('El ID de usuario es requerido y debe ser un número entero.', 400);
        }

        // 3. Ejecutar caso de uso
        $userId = (int)$data['userId'];

        try {
            $profile = $this->getCitizenProfileUseCase->execute($userId);
            return $this->ok($profile);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function getDriverProfile(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Obtener datos
        $data = ['userId' => $request->getAttribute('userId')];

        // 2. Validar datos
        if (!isset($data['userId'])) {
            return $this->error('El ID de usuario es requerido y debe ser un número entero.', 400);
        }

        // 3. Ejecutar caso de uso
        $userId = (int)$data['userId'];

        try {
            $profile = $this->getDriverProfileUseCase->execute($userId);
            return $this->ok($profile);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
