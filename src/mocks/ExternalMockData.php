<?php

namespace itaxcix\mocks;

class ExternalMockData {
    public static function getDNI(string $dni): array {
        return [
            'success' => true,
            'message' => 'Datos obtenidos correctamente.',
            'data' => [
                'nombre_completo' => 'Juan Pérez Díaz',
                'tipo_documento' => 'DNI',
                'documento' => $dni,
                'nacionalidad' => 'Peruana',
                'fecha_nacimiento' => '1990-05-15',
                'sexo' => 'Masculino',
                'estado_civil' => 'Soltero',
                'direccion' => 'Av. Javier Prado 123',
                'ubigeo' => '150101',
                'telefono' => '+51987654321',
                'email' => 'juan.perez@example.com'
            ]
        ];
    }

    public static function getRUC(string $ruc): array {
        return [
            'success' => true,
            'message' => 'Datos obtenidos correctamente.',
            'data' => [
                'razon_social' => 'EMPRESA DE PRUEBA S.A.C.',
                'tipo_documento' => 'RUC',
                'documento' => $ruc,
                'nacionalidad' => 'Peruana',
                'fecha_registro' => '2010-03-15',
                'direccion' => 'Av. Los Incas 456',
                'ubigeo' => '150138',
                'telefono' => '999888777',
                'email' => 'contacto@empresaprueba.com'
            ]
        ];
    }

    public static function getCarnetExtranjeria(string $carnet): array {
        return [
            'success' => true,
            'message' => 'Datos obtenidos correctamente.',
            'data' => [
                'nombre_completo' => 'Carlos Mendoza Díaz',
                'tipo_documento' => 'Carné de Extranjería',
                'documento' => $carnet,
                'nacionalidad' => 'Colombiana',
                'fecha_nacimiento' => '1985-08-22',
                'sexo' => 'Masculino',
                'estado_civil' => 'Soltero',
                'direccion' => 'Jr. Los Olivos 456',
                'ubigeo' => '140106',
                'telefono' => '+51976543210',
                'email' => 'carlos.mendoza@example.org'
            ]
        ];
    }

    public static function getPasaporte(string $pasaporte): array {
        return [
            'success' => true,
            'message' => 'Datos obtenidos correctamente.',
            'data' => [
                'nombre_completo' => 'María González Díaz',
                'tipo_documento' => 'Pasaporte',
                'documento' => $pasaporte,
                'nacionalidad' => 'Argentina',
                'fecha_nacimiento' => '1995-03-10',
                'sexo' => 'Femenino',
                'estado_civil' => 'Soltera',
                'direccion' => 'Calle Falsa 123',
                'ubigeo' => null,
                'telefono' => '+541123456789',
                'email' => 'maria.gonzalez@example.net'
            ]
        ];
    }

    public static function getErrorDocumentoNoSoportado(): array {
        return [
            'success' => false,
            'message' => 'Tipo de documento no soportado.',
            'data' => null
        ];
    }
}