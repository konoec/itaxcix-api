<?php

namespace itaxcix\Infrastructure\Web\Controller\api;

use InvalidArgumentException;
use itaxcix\Infrastructure\Web\Controller\generic\AbstractController;
use itaxcix\Shared\DTO\useCases\VehicleValidationRequestDTO;
use itaxcix\Shared\Validators\useCases\VehicleValidationValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class VehicleValidationController extends AbstractController {
    public function validateVehicleWithDocument(ServerRequestInterface $request): ResponseInterface {
        try {
            // 1. Obtener datos del cuerpo JSON
            $data = $this->getJsonBody($request);

            // 2. Validar datos de entrada
            $validator = new VehicleValidationValidator();
            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Si hay errores, devolver el primer error
                return $this->error(reset($errors), 400);
            }

            // 3. Mapear al DTO de entrada
            $dto = new VehicleValidationRequestDTO(
                documentTypeId: (int) $data['documentTypeId'],
                documentValue: (string) $data['documentValue'],
                plateValue: (string) $data['plateValue']
            );

            // 4. Llamar a lógica de validación (aquí iría el caso de uso)
            $result = $this->fakeVehicleValidationService($dto);

            // 5. Devolver resultado exitoso
            return $this->ok($result);

        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Simulación de servicio de validación de vehículo + documento.
     */
    private function fakeVehicleValidationService(VehicleValidationRequestDTO $dto): array {
        // Aquí podría haber reglas complejas según el tipo de documento o la placa

        return [
            'document' => [
                'typeId' => $dto->documentTypeId,
                'value' => $dto->documentValue,
                'valid' => ""
            ],
            'vehicle' => [
                'plate' => $dto->plateValue,
                'validPlate' => ""
            ],
            'valid' => "",
            'message' => ""
                ? 'Documento y placa válidos'
                : 'Documento y/o placa inválidos'
        ];
    }
}