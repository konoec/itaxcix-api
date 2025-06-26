<?php

namespace itaxcix\Shared\Validators\useCases\HelpCenter;

class HelpCenterValidator {
    public function validate(array $data): array {
        $errors = [];

        if (!isset($data['title']) || trim($data['title']) === '') {
            $errors['title'] = 'El título es requerido.';
        } elseif (strlen($data['title']) > 100) {
            $errors['title'] = 'El título no puede tener más de 100 caracteres.';
        }

        if (!isset($data['subtitle']) || trim($data['subtitle']) === '') {
            $errors['subtitle'] = 'El subtítulo es requerido.';
        } elseif (strlen($data['subtitle']) > 150) {
            $errors['subtitle'] = 'El subtítulo no puede tener más de 150 caracteres.';
        }

        if (!isset($data['answer']) || trim($data['answer']) === '') {
            $errors['answer'] = 'La respuesta es requerida.';
        }

        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El campo activo debe ser verdadero o falso.';
        }

        return $errors;
    }
}
