<?php

namespace itaxcix\Core\Handler\User;

use DateTime;
use InvalidArgumentException;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Domain\location\DepartmentModel;
use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Domain\location\ProvinceModel;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Domain\user\UserRoleModel;
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
use itaxcix\Core\Domain\vehicle\VehicleUserModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
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
use itaxcix\Core\UseCases\User\CitizenToDriverUseCase;
use itaxcix\Shared\DTO\client\VehicleDTO;
use itaxcix\Shared\DTO\client\VehicleResponseDTO;
use itaxcix\Shared\DTO\useCases\User\CitizenToDriverRequestDTO;
use itaxcix\Shared\DTO\useCases\User\CitizenToDriverResponseDTO;

class CitizenToDriverUseCaseHandler implements CitizenToDriverUseCase
{
    private UserRepositoryInterface $userRepository;
    private CitizenProfileRepositoryInterface $citizenProfileRepository;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private DriverStatusRepositoryInterface $driverStatusRepository;
    private VehicleRepositoryInterface $vehicleRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private RoleRepositoryInterface $roleRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    
    // Repositorios para la validación de vehículos
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
        UserRepositoryInterface $userRepository,
        CitizenProfileRepositoryInterface $citizenProfileRepository,
        DriverProfileRepositoryInterface $driverProfileRepository,
        DriverStatusRepositoryInterface $driverStatusRepository,
        VehicleRepositoryInterface $vehicleRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository,
        RoleRepositoryInterface $roleRepository,
        UserRoleRepositoryInterface $userRoleRepository,
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
        $this->userRepository = $userRepository;
        $this->citizenProfileRepository = $citizenProfileRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->driverStatusRepository = $driverStatusRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
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

    public function execute(CitizenToDriverRequestDTO $dto): CitizenToDriverResponseDTO
    {
        // 1. Verificar que el usuario existe
        $user = $this->userRepository->findUserById($dto->getUserId());
        if (!$user) {
            throw new InvalidArgumentException('El usuario no existe.');
        }

        // 2. Verificar que el usuario tiene perfil de ciudadano
        $citizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($dto->getUserId());
        if (!$citizenProfile) {
            throw new InvalidArgumentException('El usuario debe tener un perfil de ciudadano para solicitar ser conductor.');
        }

        // 3. Verificar que el usuario no tiene ya un perfil de conductor
        $existingDriverProfile = $this->driverProfileRepository->findDriverProfileByUserId($dto->getUserId());
        if ($existingDriverProfile) {
            throw new InvalidArgumentException('El usuario ya tiene un perfil de conductor.');
        }

        // 4. Validar y obtener/crear el vehículo usando la misma lógica que el registro inicial
        $vehicle = $this->validateAndGetVehicle($dto->getPlateValue());

        // 5. Verificar que el vehículo no está asignado a otro usuario
        $existingVehicleUser = $this->vehicleUserRepository->findVehicleUserByVehicleId($vehicle->getId());
        if ($existingVehicleUser) {
            throw new InvalidArgumentException('El vehículo ya está asignado a otro usuario.');
        }

        // 6. Obtener el estado "PENDIENTE" para el conductor
        $pendingStatus = $this->driverStatusRepository->findDriverStatusByName('PENDIENTE');
        if (!$pendingStatus) {
            throw new InvalidArgumentException('No se pudo encontrar el estado pendiente para conductores.');
        }

        // 7. Crear perfil de conductor en estado PENDIENTE
        $driverProfile = new DriverProfileModel(
            id: null,
            user: $user,
            status: $pendingStatus,
            averageRating: 0.00,
            ratingCount: 0
        );

        $savedDriverProfile = $this->driverProfileRepository->saveDriverProfile($driverProfile);

        // 8. Asociar vehículo al usuario
        $vehicleUser = new VehicleUserModel(
            id: null,
            user: $user,
            vehicle: $vehicle,
            active: true
        );

        $this->vehicleUserRepository->saveVehicleUser($vehicleUser);

        // 9. Asignar rol de CONDUCTOR
        $driverRole = $this->roleRepository->findRoleByName('CONDUCTOR');
        if (!$driverRole) {
            throw new InvalidArgumentException('No se pudo encontrar el rol de conductor.');
        }

        // Verificar si ya tiene el rol de conductor
        $existingDriverRole = $this->userRoleRepository->findByUserAndRole($user, $driverRole);
        if (!$existingDriverRole) {
            $userRole = new UserRoleModel(
                id: null,
                role: $driverRole,
                user: $user,
                active: true
            );

            $this->userRoleRepository->saveUserRole($userRole);
        }

        return new CitizenToDriverResponseDTO(
            userId: $dto->getUserId(),
            status: 'PENDIENTE',
            message: 'Solicitud para ser conductor enviada correctamente. Esperando aprobación del administrador.',
            driverProfileId: $savedDriverProfile->getId()
        );
    }

    private function validateAndGetVehicle(string $plateValue): VehicleModel
    {
        // Verificar si el vehículo ya está registrado
        $vehicle = $this->vehicleRepository->findAllVehicleByPlate($plateValue);

        if ($vehicle) {
            // El vehículo está registrado
            if ($vehicle->isActive() === false) {
                throw new InvalidArgumentException('La placa del vehículo ingresado está desactivada. Contacte al administrador.');
            }

            return $vehicle;
        }

        // Si el vehículo no está registrado, consultamos la API municipal y lo creamos
        $data = $this->fakeMunicipalApi($plateValue);

        if (!$data->found) {
            throw new InvalidArgumentException('La placa ingresada no existe en el sistema municipal.');
        }

        return $this->createVehicleFromApiData($data->vehicles);
    }

    private function createVehicleFromApiData(array $vehicleDTOs): VehicleModel
    {
        $vehicle = null;

        foreach ($vehicleDTOs as $index => $vehicleDTO) {
            // En la primera iteración creamos el vehículo principal
            if ($index === 0) {
                $vehicle = $this->createVehicleEntity($vehicleDTO);
            }

            // Para todas las iteraciones, creamos los procedimientos TUC
            $this->createTucProcedure($vehicle, $vehicleDTO);
        }

        return $vehicle;
    }

    private function createVehicleEntity(VehicleDTO $vehicleDTO): VehicleModel
    {
        // MARCA
        $brandModel = $this->brandRepository->findAllBrandByName($vehicleDTO->marca);
        if ($brandModel === null) {
            $brandModel = new BrandModel(
                id: null,
                name: $vehicleDTO->marca,
                active: true
            );
            $brandModel = $this->brandRepository->saveBrand($brandModel);
        }

        // MODELO
        $modelModel = $this->modelRepository->findAllModelByName($vehicleDTO->modelo);
        if ($modelModel === null) {
            $modelModel = new ModelModel(
                id: null,
                name: $vehicleDTO->modelo,
                brand: $brandModel,
                active: true
            );
            $modelModel = $this->modelRepository->saveModel($modelModel);
        }

        // COLOR
        $colorModel = $this->colorRepository->findAllColorByName($vehicleDTO->color);
        if ($colorModel === null) {
            $colorModel = new ColorModel(
                id: null,
                name: $vehicleDTO->color,
                active: true
            );
            $colorModel = $this->colorRepository->saveColor($colorModel);
        }

        // TIPO DE COMBUSTIBLE
        $fuelTypeModel = $this->fuelTypeRepository->findAllFuelTypeByName($vehicleDTO->tipoCombust);
        if ($fuelTypeModel === null) {
            $fuelTypeModel = new FuelTypeModel(
                id: null,
                name: $vehicleDTO->tipoCombust,
                active: true
            );
            $fuelTypeModel = $this->fuelTypeRepository->saveFuelType($fuelTypeModel);
        }

        // CLASE
        $vehicleClassModel = $this->vehicleClassRepository->findAllVehicleClassByName($vehicleDTO->clase);
        if ($vehicleClassModel === null) {
            $vehicleClassModel = new VehicleClassModel(
                id: null,
                name: $vehicleDTO->clase,
                active: true
            );
            $vehicleClassModel = $this->vehicleClassRepository->saveVehicleClass($vehicleClassModel);
        }

        // CATEGORÍA
        $vehicleCategoryModel = $this->vehicleCategoryRepository->findAllVehicleCategoryByName($vehicleDTO->categoria);
        if ($vehicleCategoryModel === null) {
            $vehicleCategoryModel = new VehicleCategoryModel(
                id: null,
                name: $vehicleDTO->categoria,
                active: true
            );
            $vehicleCategoryModel = $this->vehicleCategoryRepository->saveVehicleCategory($vehicleCategoryModel);
        }

        // VEHÍCULO
        $vehicle = new VehicleModel(
            id: null,
            licensePlate: $vehicleDTO->placa,
            model: $modelModel,
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
            id: null,
            vehicle: $vehicle,
            dryWeight: $vehicleDTO->pesoSeco,
            grossWeight: $vehicleDTO->pesoBruto,
            length: $vehicleDTO->longitud,
            height: $vehicleDTO->altura,
            width: $vehicleDTO->anchura,
            payloadCapacity: $vehicleDTO->cargaUtil
        );

        $this->technicalSpecificationRepository->saveTechnicalSpecification($technicalSpecification);

        return $vehicle;
    }

    private function createTucProcedure(VehicleModel $vehicle, VehicleDTO $vehicleDTO): void
    {
        $districtModel = null;
        $empresa = null;
        $serviceType = null;
        $tucModality = null;
        $tucStatus = null;
        $procedureType = null;

        // Procesar ubicación si existe
        if ($vehicleDTO->ubigeo) {
            $districtModel = $this->processLocation($vehicleDTO);
        }

        // Procesar empresa si existe
        if ($vehicleDTO->rucEmpresa) {
            $empresa = $this->companyRepository->findCompanyByRuc($vehicleDTO->rucEmpresa);
            if ($empresa === null) {
                $empresa = new CompanyModel(
                    id: null,
                    ruc: $vehicleDTO->rucEmpresa,
                    name: null,
                    active: true
                );
                $empresa = $this->companyRepository->saveCompany($empresa);
            }
        }

        // Procesar tipo de servicio
        if ($vehicleDTO->tipServ) {
            $serviceType = $this->serviceTypeRepository->findAllServiceTypeByName($vehicleDTO->tipServ);
            if ($serviceType === null) {
                $serviceType = new ServiceTypeModel(
                    id: null,
                    name: $vehicleDTO->tipServ,
                    active: true
                );
                $serviceType = $this->serviceTypeRepository->saveServiceType($serviceType);
            }
        }

        // Procesar modalidad TUC
        if ($vehicleDTO->modalidad) {
            $tucModality = $this->tucModalityRepository->findAllTucModalityByName($vehicleDTO->modalidad);
            if ($tucModality === null) {
                $tucModality = new TucModalityModel(
                    id: null,
                    name: $vehicleDTO->modalidad,
                    active: true
                );
                $tucModality = $this->tucModalityRepository->saveTucModality($tucModality);
            }
        }

        // Procesar estado TUC
        if ($vehicleDTO->fechaCaduc) {
            $fechaCaducidad = DateTime::createFromFormat('Ymd', $vehicleDTO->fechaCaduc);
            $statusName = ($fechaCaducidad === false || $fechaCaducidad < new DateTime()) ? 'VENCIDO' : 'ACTIVO';
            
            $tucStatus = $this->tucStatusRepository->findAllTucStatusByName($statusName);
            if ($tucStatus === null) {
                $tucStatus = new TucStatusModel(
                    id: null,
                    name: $statusName,
                    active: true
                );
                $tucStatus = $this->tucStatusRepository->saveTucStatus($tucStatus);
            }
        }

        // Procesar tipo de trámite
        if ($vehicleDTO->tramite) {
            $procedureType = $this->procedureTypeRepository->findAllProcedureTypeByName($vehicleDTO->tramite);
            if ($procedureType === null) {
                $procedureType = new ProcedureTypeModel(
                    id: null,
                    name: $vehicleDTO->tramite,
                    active: true
                );
                $procedureType = $this->procedureTypeRepository->saveProcedureType($procedureType);
            }
        }

        // Crear procedimiento TUC
        $procedureDate = DateTime::createFromFormat('Ymd', $vehicleDTO->fechaTram);
        $issueDate = DateTime::createFromFormat('Ymd', $vehicleDTO->fechaEmi);
        $expirationDate = DateTime::createFromFormat('Ymd', $vehicleDTO->fechaCaduc);

        $tucProcedure = new TucProcedureModel(
            id: null,
            vehicle: $vehicle,
            company: $empresa,
            district: $districtModel,
            status: $tucStatus,
            type: $procedureType,
            modality: $tucModality,
            procedureDate: $procedureDate,
            issueDate: $issueDate,
            expirationDate: $expirationDate
        );

        $tucProcedure = $this->tucProcedureRepository->saveTucProcedure($tucProcedure);

        // Crear ruta de servicio si existe
        if ($vehicleDTO->ruta) {
            $serviceRoute = new ServiceRouteModel(
                id: null,
                procedure: $tucProcedure,
                serviceType: $serviceType,
                text: $vehicleDTO->ruta,
                active: true
            );
            $this->serviceRouteRepository->saveServiceRoute($serviceRoute);
        }
    }

    private function processLocation(VehicleDTO $vehicleDTO): ?DistrictModel
    {
        $ubigeo = $vehicleDTO->ubigeo;
        $departmentUbigeo = substr($ubigeo, 0, 2);
        $provinceUbigeo = substr($ubigeo, 2, 2);
        $districtUbigeo = substr($ubigeo, 4, 2);

        // Obtener el departamento
        $departmentModel = $this->departmentRepository->findDepartmentByName($vehicleDTO->departamento);
        if ($departmentModel === null) {
            $departmentModel = new DepartmentModel(
                id: null,
                name: $vehicleDTO->departamento,
                ubigeo: $departmentUbigeo
            );
            $departmentModel = $this->departmentRepository->saveDepartment($departmentModel);
        }

        // Obtener la provincia
        $provinceModel = $this->provinceRepository->findProvinceByName($vehicleDTO->provincia);
        if ($provinceModel === null) {
            $provinceModel = new ProvinceModel(
                id: null,
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
                id: null,
                name: $vehicleDTO->distrito,
                province: $provinceModel,
                ubigeo: $districtUbigeo
            );
            $districtModel = $this->districtRepository->saveDistrict($districtModel);
        }

        return $districtModel;
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
            throw new InvalidArgumentException('El archivo CSV no tiene un formato válido.');
        }

        $headers = array_map('trim', $headers);

        $matchingVehicles = [];
        while (($row = fgetcsv($file)) !== false) {
            if (count($headers) !== count($row)) {
                continue;
            }

            $rowData = array_combine($headers, $row);

            if (isset($rowData['PLACA']) && strtoupper($rowData['PLACA']) === strtoupper($plateValue)) {
                $matchingVehicles[] = VehicleDTO::fromArray($rowData);
            }
        }

        fclose($file);

        return empty($matchingVehicles)
            ? VehicleResponseDTO::notFound()
            : VehicleResponseDTO::found($matchingVehicles);
    }
}
