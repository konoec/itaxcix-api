<?php

namespace itaxcix\Shared\Validators\Admin\User;

class AssignRolesToUserValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validar userId (requerido)
        if (!isset($data['userId']) || empty($data['userId'])) {
            $errors['userId'] = 'El ID del usuario es requerido';
        } elseif (!is_numeric($data['userId']) || (int)$data['userId'] < 1) {
            $errors['userId'] = 'El ID del usuario debe ser un número válido mayor a 0';
        }

        // Validar roleIds (requerido)
        if (!isset($data['roleIds']) || !is_array($data['roleIds'])) {
            $errors['roleIds'] = 'Los IDs de roles son requeridos y deben ser un array';
        } else {
            // Validar que todos los elementos del array sean números válidos
            foreach ($data['roleIds'] as $index => $roleId) {
                if (!is_numeric($roleId) || (int)$roleId < 1) {
                    $errors['roleIds'] = "Todos los IDs de roles deben ser números válidos mayores a 0";
                    break;
                }
            }

            // Validar que no haya duplicados
            if (count($data['roleIds']) !== count(array_unique($data['roleIds']))) {
                $errors['roleIds'] = 'No se permiten IDs de roles duplicados';
            }
        }

        return $errors;
    }
}
