<?php

namespace itaxcix\Shared\Validators\Admin\Role;

class AssignPermissionsToRoleValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validar roleId (requerido)
        if (!isset($data['roleId']) || empty($data['roleId'])) {
            $errors['roleId'] = 'El ID del rol es requerido';
        } elseif (!is_numeric($data['roleId']) || (int)$data['roleId'] < 1) {
            $errors['roleId'] = 'El ID del rol debe ser un número válido mayor a 0';
        }

        // Validar permissionIds (requerido)
        if (!isset($data['permissionIds']) || !is_array($data['permissionIds'])) {
            $errors['permissionIds'] = 'Los IDs de permisos son requeridos y deben ser un array';
        } else {
            // Validar que todos los elementos del array sean números válidos
            foreach ($data['permissionIds'] as $index => $permissionId) {
                if (!is_numeric($permissionId) || (int)$permissionId < 1) {
                    $errors['permissionIds'] = "Todos los IDs de permisos deben ser números válidos mayores a 0";
                    break;
                }
            }

            // Validar que no haya duplicados
            if (count($data['permissionIds']) !== count(array_unique($data['permissionIds']))) {
                $errors['permissionIds'] = 'No se permiten IDs de permisos duplicados';
            }
        }

        return $errors;
    }
}
