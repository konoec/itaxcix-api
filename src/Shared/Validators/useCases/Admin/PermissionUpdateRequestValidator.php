<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

class PermissionUpdateRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (!isset($data['id']) || !is_int($data['id']) || $data['id'] <= 0) {
            $errors['id'] = 'El ID del permiso es requerido y debe ser un entero positivo.';
        }
        if (!isset($data['name']) || trim($data['name']) === '') {
            $errors['name'] = 'El nombre del permiso es requerido.';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'El nombre del permiso debe tener al menos 3 caracteres.';
        }
        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El campo active debe ser booleano.';
        }
        if (isset($data['web']) && !is_bool($data['web'])) {
            $errors['web'] = 'El campo web debe ser booleano.';
        }
        return $errors;
    }
}
