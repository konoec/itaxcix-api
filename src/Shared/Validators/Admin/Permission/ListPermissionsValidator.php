<?php

namespace itaxcix\Shared\Validators\Admin\Permission;

class ListPermissionsValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validar page
        if (isset($data['page'])) {
            if (!is_numeric($data['page']) || (int)$data['page'] < 1) {
                $errors['page'] = 'La página debe ser un número mayor a 0';
            }
        }

        // Validar limit
        if (isset($data['limit'])) {
            if (!is_numeric($data['limit']) || (int)$data['limit'] < 1 || (int)$data['limit'] > 100) {
                $errors['limit'] = 'El límite debe ser un número entre 1 y 100';
            }
        }

        // Validar search
        if (isset($data['search']) && !empty($data['search'])) {
            if (strlen($data['search']) < 2) {
                $errors['search'] = 'La búsqueda debe tener al menos 2 caracteres';
            }
        }

        // Validar webOnly
        if (isset($data['webOnly'])) {
            if (!is_bool($data['webOnly']) && !in_array($data['webOnly'], ['true', 'false', '1', '0'])) {
                $errors['webOnly'] = 'webOnly debe ser un valor booleano';
            }
        }

        // Validar activeOnly
        if (isset($data['activeOnly'])) {
            if (!is_bool($data['activeOnly']) && !in_array($data['activeOnly'], ['true', 'false', '1', '0'])) {
                $errors['activeOnly'] = 'activeOnly debe ser un valor booleano';
            }
        }

        return $errors;
    }
}
