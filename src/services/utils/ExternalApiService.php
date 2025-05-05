<?php

namespace itaxcix\services\utils;

class ExternalApiService {
    public function getPerson(string $documentType, string $documentNumber): ?array
    {
        // Simulación de delay de red (opcional)
        // usleep(500000); // 0.5 segundos de espera

        // Caso exitoso: DNI
        if ($documentType === '1' && preg_match('/^\d{8}$/', $documentNumber)) {
            return [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'documento' => $documentNumber,
                'tipo_documento' => 'DNI',
                'nacionalidad' => 'Peruana',
                'fecha_nacimiento' => '1990-05-15',
                'direccion' => 'Av. Javier Prado 123',
                'telefono' => '+51987654321',
                'email' => 'juan.perez@example.com'
            ];
        }

        // Caso exitoso: Carné de Extranjería
        if ($documentType === '3' && preg_match('/^[A-Za-z0-9]{8,12}$/', $documentNumber)) {
            return [
                'nombre' => 'Carlos',
                'apellido' => 'Mendoza',
                'tipo_documento' => 'Carné de Extranjería',
                'documento' => $documentNumber,
                'nacionalidad' => 'Colombiana',
                'fecha_nacimiento' => '1985-08-22',
                'direccion' => 'Jr. Los Olivos 456',
                'telefono' => '+51976543210',
                'email' => 'carlos.mendoza@example.org'
            ];
        }

        // Caso exitoso: Pasaporte con formato válido
        if ($documentType === '2' && preg_match('/^[A-Z]{1}[0-9]{7}$/', $documentNumber)) {
            return [
                'nombre' => 'María',
                'apellido' => 'González',
                'tipo_documento' => 'Pasaporte',
                'documento' => $documentNumber,
                'nacionalidad' => 'Argentina',
                'fecha_nacimiento' => '1995-03-10',
                'direccion' => 'Calle Falsa 123',
                'telefono' => '+541123456789',
                'email' => 'maria.gonzalez@example.net'
            ];
        }

        // Caso: documento inválido o no soportado
        return null;
    }
}