<?php

namespace itaxcix\Shared\Validators\Admin\User;

class ListUsersValidator
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

        // Validar roleId
        if (isset($data['roleId']) && !empty($data['roleId'])) {
            if (!is_numeric($data['roleId']) || (int)$data['roleId'] < 1) {
                $errors['roleId'] = 'El ID del rol debe ser un número válido mayor a 0';
            }
        }

        // Validar statusId
        if (isset($data['statusId']) && !empty($data['statusId'])) {
            if (!is_numeric($data['statusId']) || (int)$data['statusId'] < 1) {
                $errors['statusId'] = 'El ID del estado debe ser un número válido mayor a 0';
            }
        }

        // Validar withWebAccess
        if (isset($data['withWebAccess'])) {
            if (!is_bool($data['withWebAccess']) && !in_array($data['withWebAccess'], ['true', 'false', '1', '0'])) {
                $errors['withWebAccess'] = 'withWebAccess debe ser un valor booleano';
            }
        }

        return $errors;
    }
}
