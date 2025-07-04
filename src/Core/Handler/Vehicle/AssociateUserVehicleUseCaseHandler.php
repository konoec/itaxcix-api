<?php

namespace itaxcix\Core\Handler\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Domain\vehicle\VehicleUserModel;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Auth\VehicleValidationValidatorUseCase;
use itaxcix\Core\UseCases\Driver\UpdateDriverTucUseCase;
use itaxcix\Core\UseCases\Vehicle\AssociateUserVehicleUseCase;
use itaxcix\Shared\DTO\useCases\Auth\VehicleValidationRequestDTO;
use itaxcix\Shared\DTO\useCases\Vehicle\AssociateVehicleRequestDto;
use itaxcix\Shared\DTO\useCases\Vehicle\AssociateVehicleResponseDto;

class AssociateUserVehicleUseCaseHandler implements AssociateUserVehicleUseCase
{
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private VehicleRepositoryInterface $vehicleRepository;
    private UserRepositoryInterface $userRepository;
    private VehicleValidationValidatorUseCase $vehicleValidationUseCase;
    private UpdateDriverTucUseCase $updateDriverTucUseCase;

    public function __construct(
        VehicleUserRepositoryInterface $vehicleUserRepository,
        VehicleRepositoryInterface $vehicleRepository,
        UserRepositoryInterface $userRepository,
        VehicleValidationValidatorUseCase $vehicleValidationUseCase,
        UpdateDriverTucUseCase $updateDriverTucUseCase
    ) {
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->userRepository = $userRepository;
        $this->vehicleValidationUseCase = $vehicleValidationUseCase;
        $this->updateDriverTucUseCase = $updateDriverTucUseCase;
    }

    public function execute(AssociateVehicleRequestDto $dto): AssociateVehicleResponseDto
    {
        // Validación 1: Verificar que el usuario existe
        $user = $this->userRepository->findUserById($dto->userId);
        if (!$user) {
            throw new InvalidArgumentException('Usuario no encontrado.');
        }

        // Validación 2: Verificar que el usuario no tenga una relación activa
        $existingActiveRelation = $this->vehicleUserRepository->findVehicleUserByUserId($dto->userId);
        if ($existingActiveRelation) {
            throw new InvalidArgumentException(
                'El usuario ya tiene un vehículo asociado. Debe desasociarse primero antes de asociar otro vehículo.'
            );
        }

        // Validación 3: Verificar si el vehículo ya existe en la base de datos
        $existingVehicle = $this->vehicleRepository->findAllVehicleByPlate($dto->plateValue);

        $vehicleCreated = false;
        $vehicleId = null;

        if ($existingVehicle) {
            // El vehículo existe - verificar que no tenga una relación activa
            $existingVehicleRelation = $this->vehicleUserRepository->findActiveVehicleUserByVehicleId($existingVehicle->getId());

            if ($existingVehicleRelation) {
                throw new InvalidArgumentException(
                    'Este vehículo ya está asociado a otro usuario. No se puede asociar a múltiples usuarios simultáneamente.'
                );
            }

            // Verificar que el vehículo esté activo
            if (!$existingVehicle->isActive()) {
                throw new InvalidArgumentException(
                    'Este vehículo está desactivado. Contacte al administrador.'
                );
            }

            $vehicleId = $existingVehicle->getId();
        } else {
            // El vehículo no existe - usar VehicleValidationValidator para crearlo
            try {
                $validationDto = new VehicleValidationRequestDTO(
                    documentTypeId: $user->getPerson()->getDocumentType()->getId(),
                    documentValue: $user->getPerson()->getDocument(),
                    plateValue: $dto->plateValue
                );

                $validationResult = $this->vehicleValidationUseCase->execute($validationDto);
                $vehicleId = $validationResult['vehicleId'];
                $vehicleCreated = true;
            } catch (\Exception $e) {
                throw new InvalidArgumentException(
                    'Error al validar o crear el vehículo: ' . $e->getMessage()
                );
            }
        }

        // Crear la nueva relación usuario-vehículo
        $vehicle = $this->vehicleRepository->findVehicleById($vehicleId);
        $newVehicleUser = new VehicleUserModel(
            id: null,
            user: $user,
            vehicle: $vehicle,
            active: true
        );

        $this->vehicleUserRepository->saveVehicleUser($newVehicleUser);

        // Actualizar TUCs del vehículo recién asociado
        $tucUpdateResult = $this->updateDriverTucUseCase->execute($dto->userId);

        return new AssociateVehicleResponseDto(
            userId: $dto->userId,
            vehicleId: $vehicleId,
            plateValue: $dto->plateValue,
            vehicleCreated: $vehicleCreated,
            tucsUpdated: $tucUpdateResult->tucsUpdated,
            message: $vehicleCreated
                ? "Vehículo creado y asociado exitosamente. Se actualizaron {$tucUpdateResult->tucsUpdated} TUCs."
                : "Vehículo asociado exitosamente. Se actualizaron {$tucUpdateResult->tucsUpdated} TUCs."
        );
    }
}
