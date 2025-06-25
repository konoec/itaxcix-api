<?php

namespace itaxcix\Shared\Validators\useCases\Admin;

class UserRoleDeleteRequestValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['id'])) {
            $errors['id'] = 'El ID de la asignación es requerido.';
        }
        return $errors;
    }
}

