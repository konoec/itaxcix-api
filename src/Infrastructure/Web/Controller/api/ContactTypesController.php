<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\ContactTypeResponseDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContactTypesController extends AbstractController {
    public function getContactTypes(ServerRequestInterface $request): ResponseInterface {
        // Simulamos obtener tipos de contacto
        $documentTypes = [
            new ContactTypeResponseDTO(id: 1, name: 'Teléfono'),
            new ContactTypeResponseDTO(id: 2, name: 'Correo Electrónico'),
        ];

        return $this->ok($documentTypes);
    }
}