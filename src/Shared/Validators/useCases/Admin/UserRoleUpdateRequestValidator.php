<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

class UserRoleUpdateRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['id'])) {
            $errors['id'] = 'El ID de la asignación es requerido.';
        }
        if (empty($data['userId'])) {
            $errors['userId'] = 'El usuario es requerido.';
        }
        if (empty($data['roleId'])) {
            $errors['roleId'] = 'El rol es requerido.';
        }
        return $errors;
    }
}

