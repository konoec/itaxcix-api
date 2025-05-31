<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Core\UseCases\DocumentValidationUseCase;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\DocumentValidationRequestDTO;
use itaxcix\Shared\Validators\useCases\DocumentValidationValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DocumentValidationController extends AbstractController {

    private DocumentValidationUseCase $documentValidationUseCase;

    public function __construct(DocumentValidationUseCase $documentValidationUseCase)
    {
        $this->documentValidationUseCase = $documentValidationUseCase;
    }

    public function validateDocument(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos de entrada
            $validator = new DocumentValidationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new DocumentValidationRequestDTO(
                documentTypeId: (int) $data['documentTypeId'],
                documentValue: (string) $data['documentValue']
            );

            // 4. Lógica de validación
            $result = $this->documentValidationUseCase->execute($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}