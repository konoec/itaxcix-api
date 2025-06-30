<?php

namespace itaxcix\Infrastructure\Web\Controller\web\Admin;

use itaxcix\Core\UseCases\Admin\Dashboard\GetDashboardStatsUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AdminDashboardController - Controlador para dashboard administrativo
 *
 * Este controlador proporciona estadísticas generales del sistema para
 * el panel de administración web. Incluye métricas importantes como:
 * - Total de usuarios y usuarios activos
 * - Total de roles y roles web
 * - Total de permisos y permisos web
 * - Usuarios con acceso web
 * - Porcentajes de actividad
 *
 * Ideal para mostrar un resumen ejecutivo del estado del sistema
 * en la página principal del panel de administración.
 *
 * @package itaxcix\Infrastructure\Web\Controller\web\Admin
 * @author Sistema de Administración iTaxCix
 */
class AdminDashboardController extends AbstractController
{
    private GetDashboardStatsUseCase $getDashboardStatsUseCase;

    public function __construct(
        GetDashboardStatsUseCase $getDashboardStatsUseCase
    ) {
        $this->getDashboardStatsUseCase = $getDashboardStatsUseCase;
    }

    #[OA\Get(
        path: "/api/v1/dashboard/stats",
        operationId: "getDashboardStats",
        description: "Obtiene estadísticas generales del sistema para el dashboard administrativo. Incluye conteos de usuarios, roles, permisos y métricas de actividad.",
        summary: "Obtener estadísticas del dashboard",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Dashboard"]
    )]
    #[OA\Response(
        response: 200,
        description: "Estadísticas del dashboard obtenidas correctamente",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "OK"),
                new OA\Property(
                    property: "data",
                    properties: [
                        new OA\Property(property: "totalUsers", type: "integer", example: 1250, description: "Total de usuarios en el sistema"),
                        new OA\Property(property: "activeUsers", type: "integer", example: 1100, description: "Usuarios activos"),
                        new OA\Property(property: "totalRoles", type: "integer", example: 8, description: "Total de roles"),
                        new OA\Property(property: "webRoles", type: "integer", example: 5, description: "Roles con acceso web"),
                        new OA\Property(property: "totalPermissions", type: "integer", example: 25, description: "Total de permisos"),
                        new OA\Property(property: "webPermissions", type: "integer", example: 15, description: "Permisos web"),
                        new OA\Property(property: "usersWithWebAccess", type: "integer", example: 45, description: "Usuarios con acceso web"),
                        new OA\Property(property: "userActivityPercentage", type: "number", format: "float", example: 88.0, description: "Porcentaje de usuarios activos"),
                        new OA\Property(property: "webAccessPercentage", type: "number", format: "float", example: 3.6, description: "Porcentaje de usuarios con acceso web")
                    ],
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 401,
        description: "No autorizado - Token inválido o expirado"
    )]
    #[OA\Response(
        response: 403,
        description: "Acceso denegado - Sin permisos de CONFIGURACIÓN"
    )]
    #[OA\Response(
        response: 500,
        description: "Error interno del servidor"
    )]
    /**
     * Obtiene estadísticas generales del sistema para el dashboard
     *
     * Endpoint: GET /api/v1/admin/dashboard/stats
     * Permisos: admin.dashboard.view
     *
     * Proporciona métricas clave del sistema:
     * - Conteos totales de entidades principales
     * - Porcentajes de actividad de usuarios
     * - Distribución de acceso web
     *
     * Respuesta exitosa (200):
     * {
     *   "success": true,
     *   "data": {
     *     "totalUsers": 1250,
     *     "activeUsers": 1100,
     *     "totalRoles": 8,
     *     "webRoles": 5,
     *     "totalPermissions": 25,
     *     "webPermissions": 15,
     *     "usersWithWebAccess": 45,
     *     "userActivityPercentage": 88.0,
     *     "webAccessPercentage": 3.6
     *   }
     * }
     *
     * @param ServerRequestInterface $request Petición HTTP
     * @return ResponseInterface Respuesta con estadísticas del sistema
     */
    public function getStats(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->getDashboardStatsUseCase->execute();
            return $this->ok($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
