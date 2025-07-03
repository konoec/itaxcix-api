<?php

namespace itaxcix\Core\Handler\Driver;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Domain\vehicle\TucProcedureModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Driver\UpdateDriverTucUseCase;
use itaxcix\Shared\DTO\client\VehicleResponseDTO;
use itaxcix\Shared\DTO\useCases\Driver\TucUpdateDto;
use itaxcix\Shared\DTO\useCases\Driver\UpdateTucResponseDto;

class UpdateDriverTucUseCaseHandler implements UpdateDriverTucUseCase
{
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private TucProcedureRepositoryInterface $tucProcedureRepository;
    private TucStatusRepositoryInterface $tucStatusRepository;
    private ProcedureTypeRepositoryInterface $procedureTypeRepository;
    private TucModalityRepositoryInterface $tucModalityRepository;
    private CompanyRepositoryInterface $companyRepository;
    private DistrictRepositoryInterface $districtRepository;

    public function __construct(
        VehicleUserRepositoryInterface $vehicleUserRepository,
        TucProcedureRepositoryInterface $tucProcedureRepository,
        TucStatusRepositoryInterface $tucStatusRepository,
        ProcedureTypeRepositoryInterface $procedureTypeRepository,
        TucModalityRepositoryInterface $tucModalityRepository,
        CompanyRepositoryInterface $companyRepository,
        DistrictRepositoryInterface $districtRepository
    ) {
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->tucProcedureRepository = $tucProcedureRepository;
        $this->tucStatusRepository = $tucStatusRepository;
        $this->procedureTypeRepository = $procedureTypeRepository;
        $this->tucModalityRepository = $tucModalityRepository;
        $this->companyRepository = $companyRepository;
        $this->districtRepository = $districtRepository;
    }

    public function execute(int $driverId): UpdateTucResponseDto
    {
        // Obtener todos los vehículos ACTIVOS del conductor
        $vehicleUsers = $this->vehicleUserRepository->findActiveVehicleUsersByUserId($driverId);

        if (empty($vehicleUsers)) {
            return new UpdateTucResponseDto(
                totalVehiclesChecked: 0,
                tucsUpdated: 0,
                tucsUpToDate: 0,
                updates: [],
                message: "El conductor no tiene vehículos activos registrados"
            );
        }

        $totalVehiclesChecked = 0;
        $tucsUpdated = 0;
        $tucsUpToDate = 0;
        $updates = [];

        foreach ($vehicleUsers as $vehicleUser) {
            $vehicle = $vehicleUser->getVehicle();
            $totalVehiclesChecked++;

            try {
                // Consultar la API municipal para verificar TUCs actualizadas
                $municipalData = $this->fakeMunicipalApi($vehicle->getLicensePlate());

                if (!$municipalData->found || empty($municipalData->vehicles)) {
                    continue;
                }

                // Obtener TODAS las TUCs existentes del vehículo en la base de datos
                $existingTucs = $this->tucProcedureRepository->findAllTucProceduresByVehicleId($vehicle->getId());

                // Crear un array de fechas de vencimiento existentes para comparación rápida
                $existingExpirationDates = [];
                foreach ($existingTucs as $existingTuc) {
                    if ($existingTuc->getExpirationDate()) {
                        $existingExpirationDates[] = $existingTuc->getExpirationDate()->format('Y-m-d');
                    }
                }

                // Procesar cada TUC encontrada en los datos municipales
                foreach ($municipalData->vehicles as $vehicleData) {
                    if (empty($vehicleData->fechaVencimiento)) {
                        continue;
                    }

                    $municipalExpirationDate = $vehicleData->fechaVencimiento;

                    // Verificar si esta TUC ya existe en la base de datos
                    if (in_array($municipalExpirationDate, $existingExpirationDates)) {
                        $tucsUpToDate++;
                        continue; // Esta TUC ya está registrada
                    }

                    // Esta es una TUC nueva, registrarla
                    $newTuc = $this->createTucFromMunicipalData($vehicle, $vehicleData);
                    $this->tucProcedureRepository->saveTucProcedure($newTuc);

                    $tucsUpdated++;
                    $updates[] = new TucUpdateDto(
                        plate: $vehicle->getLicensePlate(),
                        previousExpirationDate: null, // No hay TUC anterior específica
                        newExpirationDate: $municipalExpirationDate,
                        status: $vehicleData->estado ?? 'VIGENTE'
                    );
                }

            } catch (\Exception $e) {
                // Log del error pero continuar con otros vehículos
                error_log("Error actualizando TUC para vehículo {$vehicle->getLicensePlate()}: " . $e->getMessage());
                continue;
            }
        }

        $message = "Se verificaron {$totalVehiclesChecked} vehículos. ";
        $message .= "Se actualizaron {$tucsUpdated} TUCs. ";
        $message .= "{$tucsUpToDate} TUCs ya estaban al día.";

        return new UpdateTucResponseDto(
            totalVehiclesChecked: $totalVehiclesChecked,
            tucsUpdated: $tucsUpdated,
            tucsUpToDate: $tucsUpToDate,
            updates: $updates,
            message: $message
        );
    }

    private function createTucFromMunicipalData($vehicle, $municipalTucData): TucProcedureModel
    {
        // Obtener o crear los datos relacionados
        $tucStatus = $this->tucStatusRepository->findAllTucStatusByName('VIGENTE');
        $procedureType = $this->procedureTypeRepository->findAllProcedureTypeByName('RENOVACIÓN');
        $tucModality = $this->tucModalityRepository->findAllTucModalityByName('PRESENCIAL');

        // Buscar empresa si existe en los datos
        $company = null;
        if (!empty($municipalTucData->empresa)) {
            // Usar el método correcto que existe en DoctrineCompanyRepository
            // Nota: Este método busca por nombre, si no existe tendrías que crearlo o buscar por RUC
            $companies = $this->companyRepository->findAllCompanies();
            foreach ($companies as $comp) {
                if (strtolower($comp->getName()) === strtolower($municipalTucData->empresa)) {
                    $company = $comp;
                    break;
                }
            }
        }

        // Buscar distrito si existe en los datos
        $district = null;
        if (!empty($municipalTucData->distrito)) {
            // Usar el método correcto que existe en DoctrineDistrictRepository
            $district = $this->districtRepository->findDistrictByName($municipalTucData->distrito);
        }

        return new TucProcedureModel(
            id: null,
            vehicle: $vehicle,
            company: $company,
            district: $district,
            status: $tucStatus,
            type: $procedureType,
            modality: $tucModality,
            procedureDate: !empty($municipalTucData->fechaProcedimiento) ? DateTime::createFromFormat('Y-m-d', $municipalTucData->fechaProcedimiento) : new DateTime(),
            issueDate: !empty($municipalTucData->fechaExpedicion) ? DateTime::createFromFormat('Y-m-d', $municipalTucData->fechaExpedicion) : new DateTime(),
            expirationDate: !empty($municipalTucData->fechaVencimiento) ? DateTime::createFromFormat('Y-m-d', $municipalTucData->fechaVencimiento) : null
        );
    }

    private function fakeMunicipalApi(string $plateValue): VehicleResponseDTO
    {
        $csvFilePath = dirname(__DIR__, 4) . '/public/csv/data.csv';

        if (!file_exists($csvFilePath)) {
            throw new InvalidArgumentException('El archivo de datos de vehículos no existe.');
        }

        $file = fopen($csvFilePath, 'r');
        if (!$file) {
            throw new InvalidArgumentException('No se pudo abrir el archivo de datos de vehículos.');
        }

        $headers = fgetcsv($file);
        if (!$headers) {
            fclose($file);
            throw new InvalidArgumentException('El archivo de datos de vehículos está vacío o mal formateado.');
        }

        $vehicles = [];
        $found = false;

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);

            if ($data['placa'] === $plateValue) {
                $found = true;
                // Crear objeto VehicleDTO con los datos del CSV
                $vehicles[] = (object) $data;
            }
        }

        fclose($file);

        return new VehicleResponseDTO(
            found: $found,
            count: count($vehicles),
            vehicles: $vehicles
        );
    }
}
