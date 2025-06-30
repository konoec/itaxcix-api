<?php

namespace itaxcix\Shared\Validators\useCases\DocumentType;

use itaxcix\Shared\DTO\useCases\DocumentType\DocumentTypeRequestDTO;

class DocumentTypeValidator
{
    public static function validate(array $data): array
    {
        $errors = [];

        // Validar nombre (obligatorio)
        if (!isset($data['name']) || empty(trim($data['name']))) {
            $errors['name'] = 'El nombre del tipo de documento es obligatorio';
        } elseif (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen(trim($data['name'])) > 100) {
            $errors['name'] = 'El nombre no puede exceder 100 caracteres';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\.]+$/', trim($data['name']))) {
            $errors['name'] = 'El nombre solo puede contener letras, espacios, guiones y puntos';
        }

        // Validar active (opcional, debe ser booleano si se proporciona)
        if (isset($data['active']) && !is_bool($data['active']) && !in_array($data['active'], [0, 1, '0', '1', 'true', 'false'])) {
            $errors['active'] = 'El campo active debe ser un valor booleano';
        }

        return $errors;
    }

    public static function createDTO(array $data): DocumentTypeRequestDTO
    {
        $name = trim($data['name']);
        $active = null;

        if (isset($data['active'])) {
            if (is_bool($data['active'])) {
                $active = $data['active'];
            } elseif (in_array($data['active'], [1, '1', 'true'], true)) {
                $active = true;
            } elseif (in_array($data['active'], [0, '0', 'false'], true)) {
                $active = false;
            }
        }

        return new DocumentTypeRequestDTO($name, $active);
    }

    public static function getPredefinedTypes(): array
    {
        return [
            'documents' => [
                'Cédula de Ciudadanía',
                'Cédula de Extranjería',
                'Pasaporte',
                'DNI',
                'Tarjeta de Identidad',
                'NIT',
                'RUT',
                'Registro Civil',
                'Licencia de Conducción'
            ]
        ];
    }
}
