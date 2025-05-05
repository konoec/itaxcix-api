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

    public function getVehicleTUC(string $plate): ?array
    {
        // Validar formato de placa: 6 caracteres alfanuméricos mayúsculos
        if (!preg_match('/^[A-Z0-9]{6}$/', $plate)) {
            return null;
        }

        // Datos simulados para algunas placas específicas
        if ($plate === 'M2T511') {
            return [
                'DEPARTAMENTO' => 'LAMBAYEQUE',
                'PROVINCIA' => 'CHICLAYO',
                'DISTRITO' => 'CHICLAYO',
                'UBIGEO' => '140101',
                'GOB_LOCAL' => 'MPCH',
                'RUC' => '20141784901',
                'PLACA' => 'M2T511',
                'NUM_ASIENTOS' => '20',
                'ANIO_FABRIC' => '2013',
                'TIPO_COMBUST' => 'BI-COMBUSTIBLE',
                'PESO_SECO' => '2.550',
                'PESO_BRUTO' => '3.910',
                'LONGITUD' => '5.990',
                'ALTURA' => '2.280',
                'ANCHURA' => '1.880',
                'CARGA_UTIL' => '1.360',
                'NUM_PASAJ' => '19',
                'MARCA' => 'JOYLONG',
                'MODELO' => 'HKL6600',
                'COLOR' => 'BLANCO',
                'CLASE' => 'COMBI',
                'TRAMITE' => 'RENOVACION DE CONSECION',
                'FECHA_TRAM' => '20230117',
                'MODALIDAD' => 'CAMIONETA RURAL',
                'FECHA_EMI' => '20220813',
                'FECHA_CADUC' => '20270312',
                'RUC_EMPRESA' => '20271639717',
                'TIP_SERV' => 'INTERURBANO',
                'RUTA' => 'CHICLAYO - ZAÑA - CAYALTI  Y  VICEVERSA',
                'CATEGORIA' => 'M2',
                'PROPIETARIO' => '8b2a8ce20a8a6607a01b32aa8dda3bf2'
            ];
        }

        if ($plate === 'M2T526') {
            return [
                'DEPARTAMENTO' => 'LAMBAYEQUE',
                'PROVINCIA' => 'CHICLAYO',
                'DISTRITO' => 'CHICLAYO',
                'UBIGEO' => '140101',
                'GOB_LOCAL' => 'MPCH',
                'RUC' => '20141784901',
                'PLACA' => 'M2T526',
                'NUM_ASIENTOS' => '20',
                'ANIO_FABRIC' => '2013',
                'TIPO_COMBUST' => 'BI-COMBUSTIBLE',
                'PESO_SECO' => '2.550',
                'PESO_BRUTO' => '3.910',
                'LONGITUD' => '5.990',
                'ALTURA' => '2.280',
                'ANCHURA' => '1.880',
                'CARGA_UTIL' => '1.360',
                'NUM_PASAJ' => '19',
                'MARCA' => 'JOYLONG',
                'MODELO' => 'HKL6600',
                'COLOR' => 'BLANCO',
                'CLASE' => 'COMBI',
                'TRAMITE' => 'ACTUALIZAR DATOS AL SISTEMA',
                'FECHA_TRAM' => '20220920',
                'MODALIDAD' => 'CAMIONETA RURAL',
                'FECHA_EMI' => '20220813',
                'FECHA_CADUC' => '20250813',
                'RUC_EMPRESA' => '20271639717',
                'TIP_SERV' => 'INTERURBANO',
                'RUTA' => 'CHICLAYO - ZAÑA - CAYALTI  Y  VICEVERSA',
                'CATEGORIA' => 'M2',
                'PROPIETARIO' => 'ae3e7c2aea85358a0a60389728bc643f'
            ];
        }

        if ($plate === 'M2U359') {
            return [
                'DEPARTAMENTO' => 'LAMBAYEQUE',
                'PROVINCIA' => 'CHICLAYO',
                'DISTRITO' => 'CHICLAYO',
                'UBIGEO' => '140101',
                'GOB_LOCAL' => 'MPCH',
                'RUC' => '20141784901',
                'PLACA' => 'M2U359',
                'NUM_ASIENTOS' => '20',
                'ANIO_FABRIC' => '2013',
                'TIPO_COMBUST' => 'BI-COMBUSTIBLE',
                'PESO_SECO' => '2.530',
                'PESO_BRUTO' => '3.910',
                'LONGITUD' => '5.990',
                'ALTURA' => '2.280',
                'ANCHURA' => '1.880',
                'CARGA_UTIL' => '1.380',
                'NUM_PASAJ' => '19',
                'MARCA' => 'JOYLONG',
                'MODELO' => 'HKL6600',
                'COLOR' => 'PLATA',
                'CLASE' => 'COMBI',
                'TRAMITE' => 'RENOVACION DE CONSECION',
                'FECHA_TRAM' => '20221124',
                'MODALIDAD' => 'CAMIONETA RURAL',
                'FECHA_EMI' => '20221208',
                'FECHA_CADUC' => '20271208',
                'RUC_EMPRESA' => '20271639717',
                'TIP_SERV' => 'INTERURBANO',
                'RUTA' => 'CHICLAYO - ZAÑA - CAYALTI  Y  VICEVERSA',
                'CATEGORIA' => 'M2',
                'PROPIETARIO' => '34f3dfa7521421f23824d7838d5b4a3b'
            ];
        }

        // Si la placa no está registrada
        return null;
    }
}