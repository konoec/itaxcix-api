<?php

namespace itaxcix\Core\UseCases;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Domain\location\DepartmentModel;
use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Domain\location\ProvinceModel;
use itaxcix\Core\Domain\vehicle\BrandModel;
use itaxcix\Core\Domain\vehicle\ColorModel;
use itaxcix\Core\Domain\vehicle\FuelTypeModel;
use itaxcix\Core\Domain\vehicle\ModelModel;
use itaxcix\Core\Domain\vehicle\ProcedureTypeModel;
use itaxcix\Core\Domain\vehicle\ServiceRouteModel;
use itaxcix\Core\Domain\vehicle\ServiceTypeModel;
use itaxcix\Core\Domain\vehicle\TechnicalSpecificationModel;
use itaxcix\Core\Domain\vehicle\TucModalityModel;
use itaxcix\Core\Domain\vehicle\TucProcedureModel;
use itaxcix\Core\Domain\vehicle\TucStatusModel;
use itaxcix\Core\Domain\vehicle\VehicleCategoryModel;
use itaxcix\Core\Domain\vehicle\VehicleClassModel;
use itaxcix\Core\Domain\vehicle\VehicleModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ServiceRouteRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TechnicalSpecificationRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Shared\DTO\client\VehicleDTO;
use itaxcix\Shared\DTO\client\VehicleResponseDTO;
use itaxcix\Shared\DTO\useCases\DocumentValidationRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleValidationRequestDTO;

class VehicleValidationValidatorUseCaseHandler implements VehicleValidationValidatorUseCase
{
    private DocumentValidationUseCase $documentValidationUseCase;
    private VehicleRepositoryInterface $vehicleRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private ProvinceRepositoryInterface $provinceRepository;
    private DistrictRepositoryInterface $districtRepository;
    private BrandRepositoryInterface $brandRepository;
    private ColorRepositoryInterface $colorRepository;
    private FuelTypeRepositoryInterface $fuelTypeRepository;
    private ModelRepositoryInterface $modelRepository;
    private TechnicalSpecificationRepositoryInterface $technicalSpecificationRepository;
    private VehicleCategoryRepositoryInterface $vehicleCategoryRepository;
    private VehicleClassRepositoryInterface $vehicleClassRepository;
    private CompanyRepositoryInterface $companyRepository;
    private ServiceTypeRepositoryInterface $serviceTypeRepository;
    private TucModalityRepositoryInterface $tucModalityRepository;
    private TucStatusRepositoryInterface $tucStatusRepository;
    private ProcedureTypeRepositoryInterface $procedureTypeRepository;
    private TucProcedureRepositoryInterface $tucProcedureRepository;
    private ServiceRouteRepositoryInterface $serviceRouteRepository;
    public function __construct(
        DocumentValidationUseCase $documentValidationUseCase,
        VehicleRepositoryInterface $vehicleRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository,
        DepartmentRepositoryInterface $departmentRepository,
        ProvinceRepositoryInterface $provinceRepository,
        DistrictRepositoryInterface $districtRepository,
        BrandRepositoryInterface $brandRepository,
        ColorRepositoryInterface $colorRepository,
        FuelTypeRepositoryInterface $fuelTypeRepository,
        ModelRepositoryInterface $modelRepository,
        TechnicalSpecificationRepositoryInterface $technicalSpecificationRepository,
        VehicleCategoryRepositoryInterface $vehicleCategoryRepository,
        VehicleClassRepositoryInterface $vehicleClassRepository,
        CompanyRepositoryInterface $companyRepository,
        ServiceTypeRepositoryInterface $serviceTypeRepository,
        TucModalityRepositoryInterface $tucModalityRepository,
        TucStatusRepositoryInterface $tucStatusRepository,
        ProcedureTypeRepositoryInterface $procedureTypeRepository,
        TucProcedureRepositoryInterface $tucProcedureRepository,
        ServiceRouteRepositoryInterface $serviceRouteRepository
    ) {
        $this->documentValidationUseCase = $documentValidationUseCase;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->departmentRepository = $departmentRepository;
        $this->provinceRepository = $provinceRepository;
        $this->districtRepository = $districtRepository;
        $this->brandRepository = $brandRepository;
        $this->colorRepository = $colorRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->modelRepository = $modelRepository;
        $this->technicalSpecificationRepository = $technicalSpecificationRepository;
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
        $this->vehicleClassRepository = $vehicleClassRepository;
        $this->companyRepository = $companyRepository;
        $this->serviceTypeRepository = $serviceTypeRepository;
        $this->tucModalityRepository = $tucModalityRepository;
        $this->tucStatusRepository = $tucStatusRepository;
        $this->procedureTypeRepository = $procedureTypeRepository;
        $this->tucProcedureRepository = $tucProcedureRepository;
        $this->serviceRouteRepository = $serviceRouteRepository;
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
        $vehicle = $this->vehicleRepository->findAllVehicleByPlate($dto->documentValue);

        if ($vehicle) {
            // El vehículo está registrado
            if ($vehicle->isActive() === false) {
                throw new InvalidArgumentException('La placa del vehículo ingresado está desactivada. Contacte al administrador.');
            }

            // Verificar si el vehículo está relacionado con un usuario
            $vehicleUser = $this->vehicleUserRepository->findVehicleUserByVehicleId($vehicle->getId());

            if ($vehicleUser !== null) {
                throw new InvalidArgumentException('El vehículo ya está relacionado con un usuario.');
            }

            return [
                'personId' => $result['personId'],
                'vehicleId' => $vehicle->getId()
            ];
        }

        // Si el vehículo no está registrado, se puede registrar
        $data = $this->fakeMunicipalApi($dto->plateValue);

        if (!($data->found)) {
            throw new InvalidArgumentException('La placa ingresada no existe.');
        }

        foreach ($data->vehicles as $index => $vehicleDTO) {
            $vehicleDTO = new VehicleDTO();

            // Debo verificar si es la primera vez que se registra el vehículo, ya que los que se iteran son los relacionados a la tuc.
            if ($index === 0) {
                // MARCA
                $brandModel = $this->brandRepository->findAllBrandByName($vehicleDTO->marca);
                if ($brandModel === null) {
                    $brandModel = new BrandModel(
                        id: 0,
                        name: $vehicleDTO->marca,
                        active: true
                    );

                    $brandModel = $this->brandRepository->saveBrand($brandModel);
                }

                // MODELO
                $ModelModel = $this->modelRepository->findAllModelByName($vehicleDTO->modelo);
                if ($ModelModel === null) {
                    $ModelModel = new ModelModel(
                        id: 0,
                        name: $vehicleDTO->modelo,
                        brand: $brandModel,
                        active: true
                    );

                    $ModelModel = $this->modelRepository->saveModel($ModelModel);
                }

                // COLOR
                $colorModel = $this->colorRepository->findAllColorByName($vehicleDTO->color);
                if ($colorModel === null) {
                    $colorModel = new ColorModel(
                        id: 0,
                        name: $vehicleDTO->color,
                        active: true
                    );

                    $colorModel = $this->colorRepository->saveColor($colorModel);
                }

                // TIPO DE COMBUSTIBLE
                $fuelTypeModel = $this->fuelTypeRepository->findAllFuelTypeByName($vehicleDTO->tipoCombust);
                if ($fuelTypeModel === null) {
                    $fuelTypeModel = new FuelTypeModel(
                        id: 0,
                        name: $vehicleDTO->tipoCombust,
                        active: true
                    );

                    $fuelTypeModel = $this->fuelTypeRepository->saveFuelType($fuelTypeModel);
                }

                // CLASE
                $vehicleClassModel = $this->vehicleClassRepository->findAllVehicleClassByName($vehicleDTO->clase);
                if ($vehicleClassModel === null) {
                    $vehicleClassModel = new VehicleClassModel(
                        id: 0,
                        name: $vehicleDTO->clase,
                        active: true
                    );

                    $vehicleClassModel = $this->vehicleClassRepository->saveVehicleClass($vehicleClassModel);
                }

                // CATEGORÍA
                $vehicleCategoryModel = $this->vehicleCategoryRepository->findAllVehicleCategoryByName($vehicleDTO->categoria);
                if ($vehicleCategoryModel === null) {
                    $vehicleCategoryModel = new VehicleCategoryModel(
                        id: 0,
                        name: $vehicleDTO->categoria,
                        active: true
                    );

                    $vehicleCategoryModel = $this->vehicleCategoryRepository->saveVehicleCategory($vehicleCategoryModel);
                }

                // VEHÍCULO
                $vehicle = new VehicleModel(
                    id: 0,
                    licensePlate: $vehicleDTO->placa,
                    model: $ModelModel,
                    color: $colorModel,
                    manufactureYear: $vehicleDTO->anioFabric,
                    seatCount: $vehicleDTO->numAsientos,
                    passengerCount: $vehicleDTO->numPasaj,
                    fuelType: $fuelTypeModel,
                    vehicleClass: $vehicleClassModel,
                    category: $vehicleCategoryModel,
                    active: true
                );

                $vehicle = $this->vehicleRepository->saveVehicle($vehicle);

                // ESPECIFICACIÓN TÉCNICA
                $technicalSpecification = new TechnicalSpecificationModel(
                    id: 0,
                    vehicle: $vehicle,
                    dryWeight: $vehicleDTO->pesoSeco,
                    grossWeight: $vehicleDTO->pesoBruto,
                    length: $vehicleDTO->longitud,
                    height: $vehicleDTO->altura,
                    width: $vehicleDTO->anchura,
                    payloadCapacity: $vehicleDTO->cargaUtil
                );

                $technicalSpecification = $this->technicalSpecificationRepository->saveTechnicalSpecification($technicalSpecification);
            }

            if ($vehicleDTO->ubigeo){
                $ubigeo = $vehicleDTO->ubigeo;
                $departmentUbigeo = substr($ubigeo, 0, 2);
                $provinceUbigeo = substr($ubigeo, 2, 2);
                $districtUbigeo = substr($ubigeo, 4, 2);

                // Obtener el departamento
                $departmentModel = $this->departmentRepository->findDepartmentByName($vehicleDTO->departamento);
                if ($departmentModel === null) {
                    $departmentModel = new DepartmentModel(
                        id: 0,
                        name: $vehicleDTO->departamento,
                        ubigeo: $departmentUbigeo
                    );

                    $departmentModel = $this->departmentRepository->saveDepartment($departmentModel);
                }

                // Obtener la provincia
                $provinceModel = $this->provinceRepository->findProvinceByName($vehicleDTO->provincia);
                if ($provinceModel === null) {
                    $provinceModel = new ProvinceModel(
                        id: 0,
                        name: $vehicleDTO->provincia,
                        department: $departmentModel,
                        ubigeo: $provinceUbigeo
                    );

                    $provinceModel = $this->provinceRepository->saveProvince($provinceModel);
                }

                // Obtener el distrito
                $districtModel = $this->districtRepository->findDistrictByName($vehicleDTO->distrito);
                if ($districtModel === null) {
                    $districtModel = new DistrictModel(
                        id: 0,
                        name: $vehicleDTO->distrito,
                        province: $provinceModel,
                        ubigeo: $districtUbigeo
                    );

                    $districtModel = $this->districtRepository->saveDistrict($districtModel);
                }
            }

            if ($vehicleDTO->rucEmpresa){
                $empresa = $this->companyRepository->findCompanyByRuc($vehicleDTO->rucEmpresa);

                if ($empresa === null){
                    $empresa = new CompanyModel(
                        id: 0,
                        ruc: $vehicleDTO->rucEmpresa,
                        name: null,
                        active: true
                    );

                    $empresa = $this->companyRepository->saveCompany($empresa);
                }
            }

            if($vehicleDTO->tipServ){
                $serviceType = $this->serviceTypeRepository->findAllServiceTypeByName($vehicleDTO->tipServ);

                if ($serviceType === null) {
                    $serviceType = new ServiceTypeModel(
                        id: 0,
                        name: $vehicleDTO->tipServ,
                        active: true
                    );

                    $serviceType = $this->serviceTypeRepository->saveServiceType($serviceType);
                }
            }

            if ($vehicleDTO->modalidad){
                $tucModality = $this->tucModalityRepository->findAllTucModalityByName($vehicleDTO->modalidad);

                if ($tucModality === null) {
                    $tucModality = new TucModalityModel(
                        id: 0,
                        name: $vehicleDTO->modalidad,
                        active: true
                    );

                    $tucModality = $this->tucModalityRepository->saveTucModality($tucModality);
                }
            }

            if ($vehicleDTO->fechaCaduc) {
                $fechaCaducidad = DateTime::createFromFormat('Ymd', $vehicleDTO->fechaCaduc);

                if ($fechaCaducidad === false || $fechaCaducidad < new DateTime()) {
                    $tucStatus = $this->tucStatusRepository->findAllTucStatusByName('Caducado');

                    if ($tucStatus === null) {
                        $tucStatus = new TucStatusModel(
                            id: 0,
                            name: 'VENCIDO',
                            active: true
                        );

                        $tucStatus = $this->tucStatusRepository->saveTucStatus($tucStatus);
                    }
                }else{
                    $tucStatus = $this->tucStatusRepository->findAllTucStatusByName('ACTIVO');

                    if ($tucStatus === null) {
                        $tucStatus = new TucStatusModel(
                            id: 0,
                            name: 'ACTIVO',
                            active: true
                        );

                        $tucStatus = $this->tucStatusRepository->saveTucStatus($tucStatus);
                    }
                }
            }

            if ($vehicleDTO->tramite){
                $procedureType = $this->procedureTypeRepository->findAllProcedureTypeByName($vehicleDTO->tramite);

                if ($procedureType === null) {
                    $procedureType = new ProcedureTypeModel(
                        id: 0,
                        name: $vehicleDTO->tramite,
                        active: true
                    );

                    $procedureType = $this->procedureTypeRepository->saveProcedureType($procedureType);
                }
            }

            $procedureDate = DateTime::createFromFormat('Ymd', $vehicleDTO->fechaTram);
            $issueDate = DateTime::createFromFormat('Ymd', $vehicleDTO->fechaEmi);
            $expirationDate = DateTime::createFromFormat('Ymd', $vehicleDTO->fechaCaduc);

            $tucProcedure = new TucProcedureModel(
                id: 0,
                vehicle: $vehicle,
                company: $empresa ?? null,
                district: $districtModel ?? null,
                status: $tucStatus ?? null,
                type: $procedureType ?? null,
                modality: $tucModality ?? null,
                procedureDate: $procedureDate,
                issueDate: $issueDate,
                expirationDate: $expirationDate
            );

            $tucProcedure = $this->tucProcedureRepository->saveTucProcedure($tucProcedure);

            if ($vehicleDTO->ruta){
                $serviceRoute = new ServiceRouteModel(
                    id: 0,
                    procedure: $tucProcedure,
                    serviceType: $serviceType ?? null,
                    text: $vehicleDTO->ruta,
                    active: true
                );

                $serviceRoute = $this->serviceRouteRepository->saveServiceRoute($serviceRoute);
            }
        }

        // Retornar resultado
        return [
            'personId' => $result['personId'],
            'vehicleId' => $vehicle->getId()
        ];
    }

    private function fakeMunicipalApi(string $plateValue): VehicleResponseDTO
    {
        // Ruta al archivo CSV de datos de vehículos
        $csvFilePath = __DIR__ . '/../../../data/vehicles.csv';

        // Verificar que el archivo existe
        if (!file_exists($csvFilePath)) {
            throw new InvalidArgumentException('El archivo de datos de vehículos no existe.');
        }

        // Abrir el archivo CSV
        $file = fopen($csvFilePath, 'r');
        if (!$file) {
            throw new InvalidArgumentException('No se pudo abrir el archivo de datos de vehículos.');
        }

        // Leer la primera línea como cabeceras
        $headers = fgetcsv($file);
        if (!$headers) {
            fclose($file);
            throw new InvalidArgumentException('El archivo CSV no tiene un formato válido.');
        }

        // Buscar vehículos con la placa especificada
        $matchingVehicles = [];
        while (($row = fgetcsv($file)) !== false) {
            $rowData = array_combine($headers, $row);

            // Buscamos por la columna PLACA
            if (isset($rowData['PLACA']) && strtoupper($rowData['PLACA']) === strtoupper($plateValue)) {
                $matchingVehicles[] = VehicleDTO::fromArray($rowData);
            }
        }

        fclose($file);

        // Si no se encuentra ningún vehículo, devolver respuesta vacía
        if (empty($matchingVehicles)) {
            return VehicleResponseDTO::notFound();
        }

        // Devolver todos los vehículos encontrados
        return VehicleResponseDTO::found($matchingVehicles);
    }
}