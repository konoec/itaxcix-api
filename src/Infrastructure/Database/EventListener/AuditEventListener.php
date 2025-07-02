<?php

namespace itaxcix\Infrastructure\Database\EventListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use itaxcix\Core\Interfaces\audit\AuditLogRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\audit\AuditEntity;

class AuditEventListener
{
    private array $originalData = [];

    public function __construct(
        private AuditLogRepositoryInterface $auditRepository
    ) {}

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof AuditEntity) {
            return; // No auditar la tabla de auditoría
        }

        // Guardar datos originales antes del update
        $this->originalData[spl_object_id($entity)] = $this->extractEntityData($entity, $args->getEntityManager());
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof AuditEntity) {
            return;
        }

        $this->auditRepository->logAuditEvent(
            $this->getTableName($entity),
            'INSERT',
            $this->getCurrentUser(),
            null,
            $this->extractEntityData($entity, $args->getObjectManager())
        );
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof AuditEntity) {
            return;
        }

        $objectId = spl_object_id($entity);
        $previousData = $this->originalData[$objectId] ?? null;

        $this->auditRepository->logAuditEvent(
            $this->getTableName($entity),
            'UPDATE',
            $this->getCurrentUser(),
            $previousData,
            $this->extractEntityData($entity, $args->getObjectManager())
        );

        // Limpiar datos temporales
        unset($this->originalData[$objectId]);
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof AuditEntity) {
            return;
        }

        $this->auditRepository->logAuditEvent(
            $this->getTableName($entity),
            'DELETE',
            $this->getCurrentUser(),
            $this->extractEntityData($entity, $args->getObjectManager()),
            null
        );
    }

    private function getTableName(object $entity): string
    {
        $reflection = new \ReflectionClass($entity);
        $attributes = $reflection->getAttributes(\Doctrine\ORM\Mapping\Table::class);

        if (!empty($attributes)) {
            $tableAttribute = $attributes[0]->newInstance();
            return $tableAttribute->name;
        }

        // Fallback: usar el nombre de la clase convertido
        $className = $reflection->getShortName();
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Entity', '', $className)));
    }

    private function getCurrentUser(): string
    {
        // Intentar obtener el usuario desde diferentes fuentes
        if (isset($_SERVER['HTTP_X_USER_ID'])) {
            return $_SERVER['HTTP_X_USER_ID'];
        }

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            return $_SERVER['PHP_AUTH_USER'];
        }

        // Verificar si hay un token JWT en los headers
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $user = $this->extractUserFromToken($token);
            if ($user) {
                return $user;
            }
        }

        return 'system';
    }

    private function extractUserFromToken(string $token): ?string
    {
        try {
            // Decodificar JWT básico (sin verificar firma para auditoría)
            $parts = explode('.', $token);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode($parts[1]), true);
                return $payload['sub'] ?? $payload['user_id'] ?? $payload['username'] ?? null;
            }
        } catch (\Exception $e) {
            // Si falla la decodificación, continuar con 'system'
        }

        return null;
    }

    private function extractEntityData(object $entity, $em): array
    {
        $metadata = $em->getClassMetadata(get_class($entity));
        $data = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            $value = $metadata->getFieldValue($entity, $fieldName);
            $data[$fieldName] = $this->serializeValue($value);
        }

        // También incluir asociaciones simples (ManyToOne, OneToOne)
        foreach ($metadata->getAssociationNames() as $assocName) {
            if ($metadata->isSingleValuedAssociation($assocName)) {
                $value = $metadata->getFieldValue($entity, $assocName);
                if ($value !== null) {
                    $assocMetadata = $em->getClassMetadata($metadata->getAssociationTargetClass($assocName));
                    $data[$assocName . '_id'] = $assocMetadata->getFieldValue($value, $assocMetadata->getSingleIdentifierFieldName());
                }
            }
        }

        return $data;
    }

    private function serializeValue($value): mixed
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_object($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }
}
