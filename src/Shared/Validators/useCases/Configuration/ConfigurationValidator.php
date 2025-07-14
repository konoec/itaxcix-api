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
        return $errors;
    }

    /**
     * Valida que la clave siga el formato correcto
     */
    private function isValidKey(string $key): bool
    {
        // Acepta cualquier string no vacía
        return strlen($key) > 0;
    }
}
