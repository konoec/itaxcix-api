<?php

namespace itaxcix\Shared\Validators\Admin\User;

use itaxcix\Shared\DTO\Admin\User\CreateAdminUserRequestDTO;

class CreateAdminUserValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Validación del documento (DNI)
        if (!isset($data['document']) || empty(trim($data['document']))) {
            $errors[] = 'El documento es requerido.';
        } else {
            $document = trim($data['document']);

            // Validar que tenga exactamente 8 dígitos
            if (strlen($document) !== 8) {
                $errors[] = 'El DNI debe tener exactamente 8 dígitos.';
            }

            // Validar que solo contenga números
            if (!ctype_digit($document)) {
                $errors[] = 'El DNI debe contener solo números.';
            }
        }

        // Validación del email
        if (!isset($data['email']) || empty(trim($data['email']))) {
            $errors[] = 'El email es requerido.';
        } else {
            $email = trim($data['email']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El formato del email es inválido.';
            }

            if (strlen($email) > 100) {
                $errors[] = 'El email no puede superar los 100 caracteres.';
            }
        }

        // Validación de la contraseña
        if (!isset($data['password']) || empty(trim($data['password']))) {
            $errors[] = 'La contraseña es requerida.';
        } else {
            $password = $data['password'];

            if (strlen($password) < 8) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
            }

            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password)) {
                $errors[] = 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.';
            }
        }

        // Validación del área
        if (!isset($data['area']) || empty(trim($data['area']))) {
            $errors[] = 'El área es requerida.';
        } else {
            $area = trim($data['area']);

            if (strlen($area) > 100) {
                $errors[] = 'El área no puede superar los 100 caracteres.';
            }
        }

        // Validación del cargo
        if (!isset($data['position']) || empty(trim($data['position']))) {
            $errors[] = 'El cargo es requerido.';
        } else {
            $position = trim($data['position']);

            if (strlen($position) > 100) {
                $errors[] = 'El cargo no puede superar los 100 caracteres.';
            }
        }

        return $errors;
    }

    public function createDTO(array $data): CreateAdminUserRequestDTO
    {
        return new CreateAdminUserRequestDTO(
            document: trim($data['document']),
            email: trim($data['email']),
            password: $data['password'],
            area: trim($data['area']),
            position: trim($data['position'])
        );
    }
}
