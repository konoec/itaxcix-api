<?php

namespace itaxcix\utils;

class StringUtils {

    /**
     * Divide un nombre completo en nombres y apellidos.
     *
     * @param string $nombreCompleto
     * @return array ['nombres' => string, 'apellidos' => string]
     */
    public function splitFullName(string $nombreCompleto): array {
        $partes = array_filter(explode(' ', trim($nombreCompleto)));
        $totalPartes = count($partes);

        if ($totalPartes < 2) {
            return [
                'nombres' => $nombreCompleto,
                'apellidos' => ''
            ];
        }

        // Consideramos que los primeros N/2 son nombres, el resto apellidos
        $mitad = (int) ceil($totalPartes / 2);
        $nombres = array_slice($partes, 0, $mitad);
        $apellidos = array_slice($partes, $mitad);

        return [
            'nombres' => implode(' ', $nombres),
            'apellidos' => implode(' ', $apellidos)
        ];
    }
}