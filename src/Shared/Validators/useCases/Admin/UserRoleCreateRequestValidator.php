<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

class UserRoleCreateRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['userId'])) {
            $errors['userId'] = 'El usuario es requerido.';
        }
        if (empty($data['roleId'])) {
            $errors['roleId'] = 'El rol es requerido.';
        }
        // Puedes agregar más validaciones según tu lógica de negocio
        return $errors;
    }
}

