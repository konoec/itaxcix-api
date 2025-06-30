<?php

namespace itaxcix\Shared\Validators\useCases\Configuration;

class ConfigurationValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validar key (requerido)
        if (empty($data['key'])) {
            $errors['key'][] = 'La clave de configuración es requerida.';
        } elseif (!is_string($data['key'])) {
            $errors['key'][] = 'La clave debe ser una cadena de texto.';
        } elseif (strlen($data['key']) > 255) {
            $errors['key'][] = 'La clave no puede exceder 255 caracteres.';
        } elseif (!$this->isValidKey($data['key'])) {
            $errors['key'][] = 'La clave debe seguir el formato: categoría.nombre (ej: app.maintenance_mode).';
        }

        // Validar value (requerido)
        if (!isset($data['value'])) {
            $errors['value'][] = 'El valor de configuración es requerido.';
        } elseif (!is_string($data['value']) && !is_numeric($data['value']) && !is_bool($data['value'])) {
            $errors['value'][] = 'El valor debe ser una cadena de texto, número o booleano.';
        }

        // Validar active (opcional, por defecto true)
        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'][] = 'El campo activo debe ser un valor booleano.';
        }

        // Validaciones específicas según el tipo de configuración
        if (!empty($data['key'])) {
            $keyValidationErrors = $this->validateSpecificKey($data['key'], $data['value'] ?? '');
            if (!empty($keyValidationErrors)) {
                $errors = array_merge($errors, $keyValidationErrors);
            }
        }

        return $errors;
    }

    /**
     * Valida que la clave siga el formato correcto
     */
    private function isValidKey(string $key): bool
    {
        // Formato: categoria.nombre_configuracion
        // Permite letras, números, puntos y guiones bajos
        return preg_match('/^[a-z][a-z0-9_]*(\.[a-z][a-z0-9_]*)+$/i', $key);
    }

    /**
     * Validaciones específicas según el tipo de clave
     */
    private function validateSpecificKey(string $key, $value): array
    {
        $errors = [];

        switch ($key) {
            case 'ITAXCIX_NUMERO_EMERGENCIA':
                if (!is_numeric($value) || strlen($value) < 3 || strlen($value) > 10) {
                    $errors['value'][] = 'El número de emergencia debe ser un número de 3 a 10 dígitos.';
                }
                break;

            case 'app.maintenance_mode':
            case 'notifications.enabled':
                if (!in_array(strtolower($value), ['true', 'false', '1', '0', 'yes', 'no'])) {
                    $errors['value'][] = 'Este valor debe ser booleano (true/false, 1/0, yes/no).';
                }
                break;

            case 'system.max_upload_size':
                if (!is_numeric($value) || $value < 0) {
                    $errors['value'][] = 'El tamaño máximo de subida debe ser un número positivo.';
                }
                break;

            case 'api.rate_limit':
                if (!is_numeric($value) || $value < 1 || $value > 10000) {
                    $errors['value'][] = 'El límite de peticiones debe ser entre 1 y 10,000 por minuto.';
                }
                break;
        }

        return $errors;
    }

    /**
     * Obtiene las claves de configuración predefinidas del sistema
     */
    public static function getPredefinedKeys(): array
    {
        return [
            'Sistema' => [
                'ITAXCIX_NUMERO_EMERGENCIA' => 'Número de emergencia del sistema',
                'app.maintenance_mode' => 'Modo de mantenimiento',
                'app.name' => 'Nombre de la aplicación',
                'system.max_upload_size' => 'Tamaño máximo de archivo (MB)',
            ],
            'API' => [
                'api.rate_limit' => 'Límite de peticiones por minuto',
                'api.token_expiry' => 'Expiración de tokens (horas)',
            ],
            'Notificaciones' => [
                'notifications.enabled' => 'Notificaciones habilitadas',
                'notifications.email_enabled' => 'Notificaciones por email',
                'notifications.sms_enabled' => 'Notificaciones por SMS',
            ]
        ];
    }
}
