<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\DocumentTypeResponseDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DocumentTypesController extends AbstractController {
    public function getDocumentTypes(ServerRequestInterface $request): ResponseInterface{
        // Simulamos obtener tipos de documentos
        $documentTypes = [
            new DocumentTypeResponseDTO(id: 1, name: 'DNI'),
            new DocumentTypeResponseDTO(id: 2, name: 'Pasaporte'),
            new DocumentTypeResponseDTO(id: 3, name: 'Cédula')
        ];

        return $this->ok($documentTypes);
    }
}