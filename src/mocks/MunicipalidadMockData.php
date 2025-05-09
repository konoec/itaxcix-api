<?php

namespace itaxcix\mocks;

class MunicipalidadMockData {

    /**
     * Simulates a database query to get vehicle data by plate number.
     *
     * @param string $plate The plate number of the vehicle.
     * @return array|null The vehicle data or null if not found.
     */
    public static function getVehicleByPlate(string $plate): ?array
    {
        $vehicles = [
            'M2T511' => [
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
            ],
            'M2T526' => [
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
            ],
            'M2U359' => [
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
            ]
        ];

        return $vehicles[$plate] ?? null;
    }
}