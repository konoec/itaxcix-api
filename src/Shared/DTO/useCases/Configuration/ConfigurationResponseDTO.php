<?php

namespace itaxcix\Shared\DTO\useCases\Configuration;

use itaxcix\Core\Domain\configuration\ConfigurationModel;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ConfigurationResponseDTO",
    description: "DTO de respuesta para configuraciones del sistema",
    type: "object"
)]
class ConfigurationResponseDTO implements \JsonSerializable
{
    #[OA\Property(
        property: "id",
        description: "ID de la configuración",
        type: "integer",
        example: 1
    )]
    private int $id;

    #[OA\Property(
        property: "key",
        description: "Clave única de configuración",
        type: "string",
        example: "app.maintenance_mode"
    )]
    private string $key;

    #[OA\Property(
        property: "value",
        description: "Valor de la configuración",
        type: "string",
        example: "false"
    )]
    private string $value;

    #[OA\Property(
        property: "active",
        description: "Estado activo de la configuración",
        type: "boolean",
        example: true
    )]
    private bool $active;

    #[OA\Property(
        property: "description",
        description: "Descripción amigable de la configuración",
        type: "string",
        nullable: true,
        example: "Modo de mantenimiento de la aplicación"
    )]
    private ?string $description;

    #[OA\Property(
        property: "category",
        description: "Categoría de la configuración",
        type: "string",
        nullable: true,
        example: "Sistema"
    )]
    private ?string $category;

    public function __construct(
        int $id,
        string $key,
        string $value,
        bool $active,
        ?string $description = null,
        ?string $category = null
    ) {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
        $this->active = $active;
        $this->description = $description;
        $this->category = $category;
    }

    public static function fromModel(ConfigurationModel $model): self
    {
        return new self(
            id: $model->getId() ?? 0, // Usar 0 como fallback si el id es null
            key: $model->getKey(),
            value: $model->getValue(),
            active: $model->isActive()
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * Obtiene una descripción amigable basada en la clave de configuración
     */
    private static function getKeyDescription(string $key): ?string
    {
        $descriptions = [
            'app.maintenance_mode' => 'Modo de mantenimiento de la aplicación',
            'app.name' => 'Nombre de la aplicación',
            'app.version' => 'Versión actual de la aplicación',
            'email.smtp_host' => 'Servidor SMTP para envío de emails',
            'email.smtp_port' => 'Puerto del servidor SMTP',
            'system.max_upload_size' => 'Tamaño máximo de archivo a subir',
            'security.session_timeout' => 'Tiempo de expiración de sesión',
            'notifications.enabled' => 'Notificaciones habilitadas',
            'api.rate_limit' => 'Límite de peticiones por minuto',
        ];

        return $descriptions[$key] ?? null;
    }

    /**
     * Obtiene la categoría basada en la clave de configuración
     */
    private static function getKeyCategory(string $key): ?string
    {
        $prefix = explode('.', $key)[0] ?? '';

        $categories = [
            'app' => 'Aplicación',
            'email' => 'Correo Electrónico',
            'system' => 'Sistema',
            'security' => 'Seguridad',
            'notifications' => 'Notificaciones',
            'api' => 'API',
            'database' => 'Base de Datos',
            'storage' => 'Almacenamiento',
        ];

        return $categories[$prefix] ?? 'General';
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'active' => $this->active,
            'description' => $this->description,
            'category' => $this->category
        ];
    }
}
