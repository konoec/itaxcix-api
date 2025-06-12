<?php

use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;
use itaxcix\Core\Interfaces\incident\IncidentRepositoryInterface;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\location\CoordinatesRepositoryInterface;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\AdminProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
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
use itaxcix\Infrastructure\Database\Entity\incident\IncidentTypeEntity;
use itaxcix\Infrastructure\Database\Repository\company\DoctrineCompanyRepository;
use itaxcix\Infrastructure\Database\Repository\configuration\DoctrineConfigurationRepository;
use itaxcix\Infrastructure\Database\Repository\incident\DoctrineIncidentRepository;
use itaxcix\Infrastructure\Database\Repository\incident\DoctrineIncidentTypeRepository;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineCoordinatesRepository;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineDepartmentRepository;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineDistrictRepository;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineProvinceRepository;
use itaxcix\Infrastructure\Database\Repository\person\DoctrineDocumentTypeRepository;
use itaxcix\Infrastructure\Database\Repository\person\DoctrinePersonRepository;
use itaxcix\Infrastructure\Database\Repository\travel\DoctrineTravelRepository;
use itaxcix\Infrastructure\Database\Repository\travel\DoctrineTravelStatusRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineAdminProfileRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineCitizenProfileRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineContactTypeRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineDriverProfileRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineDriverStatusRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrinePermissionRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineRolePermissionRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineRoleRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserCodeRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserCodeTypeRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserContactRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserRoleRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserStatusRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineBrandRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineColorRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineFuelTypeRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineModelRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineProcedureTypeRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineServiceRouteRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineServiceTypeRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineTechnicalSpecificationRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineTucModalityRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineTucProcedureRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineTucStatusRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineVehicleCategoryRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineVehicleClassRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineVehicleRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineVehicleUserRepository;
use function DI\autowire;

return [
    UserRepositoryInterface::class => autowire(DoctrineUserRepository::class),
    DriverStatusRepositoryInterface::class => autowire(DoctrineDriverStatusRepository::class),
    RolePermissionRepositoryInterface::class => autowire(DoctrineRolePermissionRepository::class),
    UserRoleRepositoryInterface::class => autowire(DoctrineUserRoleRepository::class),
    ContactTypeRepositoryInterface::class => autowire(DoctrineContactTypeRepository::class),
    UserContactRepositoryInterface::class => autowire(DoctrineUserContactRepository::class),
    UserCodeTypeRepositoryInterface::class => autowire(DoctrineUserCodeTypeRepository::class),
    UserCodeRepositoryInterface::class => autowire(DoctrineUserCodeRepository::class),
    UserStatusRepositoryInterface::class => autowire(DoctrineUserStatusRepository::class),
    RoleRepositoryInterface::class => autowire(DoctrineRoleRepository::class),
    VehicleUserRepositoryInterface::class => autowire(DoctrineVehicleUserRepository::class),
    PermissionRepositoryInterface::class => autowire(DoctrinePermissionRepository::class),
    AdminProfileRepositoryInterface::class => autowire(DoctrineAdminProfileRepository::class),
    DriverProfileRepositoryInterface::class => autowire(DoctrineDriverProfileRepository::class),
    CitizenProfileRepositoryInterface::class => autowire(DoctrineCitizenProfileRepository::class),

    PersonRepositoryInterface::class => autowire(DoctrinePersonRepository::class),
    DocumentTypeRepositoryInterface::class => autowire(DoctrineDocumentTypeRepository::class),

    VehicleRepositoryInterface::class => autowire(DoctrineVehicleRepository::class),
    VehicleClassRepositoryInterface::class => autowire(DoctrineVehicleClassRepository::class),
    VehicleCategoryRepositoryInterface::class => autowire(DoctrineVehicleCategoryRepository::class),
    TechnicalSpecificationRepositoryInterface::class => autowire(DoctrineTechnicalSpecificationRepository::class),
    FuelTypeRepositoryInterface::class => autowire(DoctrineFuelTypeRepository::class),
    BrandRepositoryInterface::class => autowire(DoctrineBrandRepository::class),
    ColorRepositoryInterface::class => autowire(DoctrineColorRepository::class),
    ModelRepositoryInterface::class => autowire(DoctrineModelRepository::class),

    CompanyRepositoryInterface::class => autowire(DoctrineCompanyRepository::class),

    CoordinatesRepositoryInterface::class => autowire(DoctrineCoordinatesRepository::class),
    DepartmentRepositoryInterface::class => autowire(DoctrineDepartmentRepository::class),
    DistrictRepositoryInterface::class => autowire(DoctrineDistrictRepository::class),
    ProvinceRepositoryInterface::class => autowire(DoctrineProvinceRepository::class),

    ServiceTypeRepositoryInterface::class => autowire(DoctrineServiceTypeRepository::class),
    ServiceRouteRepositoryInterface::class => autowire(DoctrineServiceRouteRepository::class),

    TucModalityRepositoryInterface::class => autowire(DoctrineTucModalityRepository::class),
    TucStatusRepositoryInterface::class => autowire(DoctrineTucStatusRepository::class),
    TucProcedureRepositoryInterface::class => autowire(DoctrineTucProcedureRepository::class),
    ProcedureTypeRepositoryInterface::class => autowire(DoctrineProcedureTypeRepository::class),

    TravelStatusRepositoryInterface::class => autowire(DoctrineTravelStatusRepository::class),
    TravelRepositoryInterface::class => autowire(DoctrineTravelRepository::class),

    ConfigurationRepositoryInterface::class => autowire(DoctrineConfigurationRepository::class),

    IncidentRepositoryInterface::class => autowire(DoctrineIncidentRepository::class),
    IncidentTypeRepositoryInterface::class => autowire(DoctrineIncidentTypeRepository::class),
];