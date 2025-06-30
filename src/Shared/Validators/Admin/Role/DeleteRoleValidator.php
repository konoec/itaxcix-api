<?php

namespace itaxcix\Shared\Validators\Admin\Role;

class DeleteRoleValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validar id (requerido)
        if (!isset($data['id']) || empty($data['id'])) {
            $errors['id'] = 'El ID del rol es requerido';
        } elseif (!is_numeric($data['id']) || (int)$data['id'] < 1) {
            $errors['id'] = 'El ID del rol debe ser un número válido mayor a 0';
        }

        return $errors;
    }
}
