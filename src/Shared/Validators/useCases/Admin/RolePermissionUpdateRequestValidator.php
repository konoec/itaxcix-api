<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

class RolePermissionUpdateRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (!isset($data['id']) || !is_int($data['id']) || $data['id'] <= 0) {
            $errors['id'] = 'El ID de la asignación es requerido y debe ser un entero positivo.';
        }
        if (!isset($data['roleId']) || !is_int($data['roleId']) || $data['roleId'] <= 0) {
            $errors['roleId'] = 'El ID del rol es requerido y debe ser un entero positivo.';
        }
        if (!isset($data['permissionId']) || !is_int($data['permissionId']) || $data['permissionId'] <= 0) {
            $errors['permissionId'] = 'El ID del permiso es requerido y debe ser un entero positivo.';
        }
        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El campo active debe ser booleano.';
        }
        return $errors;
    }
}

