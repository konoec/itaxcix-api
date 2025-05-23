<?php

namespace itaxcix\Core\UseCases;

use InvalidArgumentException;
use itaxcix\Shared\DTO\useCases\DocumentValidationRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleValidationRequestDTO;

class VehicleValidationValidatorUseCaseHandler implements VehicleValidationValidatorUseCase
{
    private DocumentValidationUseCase $documentValidationUseCase;

    public function __construct(
        DocumentValidationUseCase $documentValidationUseCase
    ) {
        $this->documentValidationUseCase = $documentValidationUseCase;
    }

    public function execute(VehicleValidationRequestDTO $dto): ?array
    {
        // Validar primero el documento usando el otro UseCase
        $documentDTO = new DocumentValidationRequestDTO(
            documentTypeId: $dto->documentTypeId,
            documentValue: $dto->documentValue
        );

        $result = $this->documentValidationUseCase->execute($documentDTO);

        if (!$result || !isset($result['personId'])) {
            throw new InvalidArgumentException('La persona no está válidamente registrada.');
        }

        // Verificar si el vehículo está registrado
        // Verificar si el vehículo está relacionado con un usuario de manera activa
        // Solo se permite un vehículo por usuario
        // Si el vehículo no está registrado, se puede registrar
        // Si el vehículo está registrado, se puede validar
        // Si el vehículo está relacionado con un usuario, se puede validar
        // Si el vehículo no está relacionado con un usuario, se puede registrar

        // Aquí va tu lógica específica para validar el vehículo
        $data = $this->fakeMunicipalApi($dto->documentValue);

        // Retornar resultado
        return [
            'personId' => $result['personId'],
            'vehicleId' => ''
        ];
    }

    private function fakeMunicipalApi(string $plateValue): array
    {
        return [
            'name' => 'PATRICIO JESUS',
            'lastName' => 'GONZALES GONZALES',
        ];
    }
}