<?php

namespace itaxcix\services;

use itaxcix\mocks\ExternalMockData;

class ExternalService {

    /**
     * @param string $documentType
     * @param string $documentNumber
     * @return array
     */
    public function getPerson(string $documentType, string $documentNumber): array {
        return match ($documentType) {
            '1' => ExternalMockData::getDNI($documentNumber),
            '2' => ExternalMockData::getPasaporte($documentNumber),
            '3' => ExternalMockData::getCarnetExtranjeria($documentNumber),
            '4' => ExternalMockData::getRUC($documentNumber),
            default => ExternalMockData::getErrorDocumentoNoSoportado(),
        };
    }
}