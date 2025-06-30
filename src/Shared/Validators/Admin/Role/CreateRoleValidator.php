<?php

namespace itaxcix\Shared\Validators\Admin\Role;

class CreateRoleValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validar name (requerido)
        if (!isset($data['name']) || empty(trim($data['name']))) {
            $errors['name'] = 'El nombre del rol es requerido';
        } elseif (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'El nombre del rol debe tener al menos 2 caracteres';
        } elseif (strlen(trim($data['name'])) > 50) {
            $errors['name'] = 'El nombre del rol no puede exceder 50 caracteres';
        }

        // Validar active (opcional)
        if (isset($data['active'])) {
            if (!is_bool($data['active'])) {
                $errors['active'] = 'El campo activo debe ser un valor booleano';
            }
        }

        // Validar web (opcional)
        if (isset($data['web'])) {
            if (!is_bool($data['web'])) {
                $errors['web'] = 'El campo web debe ser un valor booleano';
            }
        }

        return $errors;
    }
}
