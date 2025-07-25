<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

class RolePermissionDeleteRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (!isset($data['id']) || !is_int($data['id']) || $data['id'] <= 0) {
            $errors['id'] = 'El ID de la asignación es requerido y debe ser un entero positivo.';
        }
        return $errors;
    }
}

