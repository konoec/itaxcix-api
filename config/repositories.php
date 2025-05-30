<?php

use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Core\Interfaces\person\DocumentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
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
use itaxcix\Infrastructure\Database\Repository\company\DoctrineCompanyRepository;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineDepartmentRepository;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineDistrictRepository;
use itaxcix\Infrastructure\Database\Repository\location\DoctrineProvinceRepository;
use itaxcix\Infrastructure\Database\Repository\person\DoctrineDocumentTypeRepository;
use itaxcix\Infrastructure\Database\Repository\person\DoctrinePersonRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineContactTypeRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineRolePermissionRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserCodeRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserCodeTypeRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserContactRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserRepository;
use itaxcix\Infrastructure\Database\Repository\user\DoctrineUserRoleRepository;
use itaxcix\Infrastructure\Database\Repository\vehicle\DoctrineFuelTypeRepository;
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
use function DI\autowire;

return [
    UserRepositoryInterface::class => autowire(DoctrineUserRepository::class),
    RolePermissionRepositoryInterface::class => autowire(DoctrineRolePermissionRepository::class),
    UserRoleRepositoryInterface::class => autowire(DoctrineUserRoleRepository::class),
    ContactTypeRepositoryInterface::class => autowire(DoctrineContactTypeRepository::class),
    UserContactRepositoryInterface::class => autowire(DoctrineUserContactRepository::class),
    UserCodeTypeRepositoryInterface::class => autowire(DoctrineUserCodeTypeRepository::class),
    UserCodeRepositoryInterface::class => autowire(DoctrineUserCodeRepository::class),

    PersonRepositoryInterface::class => autowire(DoctrinePersonRepository::class),
    DocumentTypeRepositoryInterface::class => autowire(DoctrineDocumentTypeRepository::class),

    VehicleRepositoryInterface::class => autowire(DoctrineVehicleRepository::class),
    VehicleClassRepositoryInterface::class => autowire(DoctrineVehicleClassRepository::class),
    VehicleCategoryRepositoryInterface::class => autowire(DoctrineVehicleCategoryRepository::class),
    TechnicalSpecificationRepositoryInterface::class => autowire(DoctrineTechnicalSpecificationRepository::class),
    FuelTypeRepositoryInterface::class => autowire(DoctrineFuelTypeRepository::class),

    CompanyRepositoryInterface::class => autowire(DoctrineCompanyRepository::class),

    DepartmentRepositoryInterface::class => autowire(DoctrineDepartmentRepository::class),
    DistrictRepositoryInterface::class => autowire(DoctrineDistrictRepository::class),
    ProvinceRepositoryInterface::class => autowire(DoctrineProvinceRepository::class),

    ServiceTypeRepositoryInterface::class => autowire(DoctrineServiceTypeRepository::class),
    ServiceRouteRepositoryInterface::class => autowire(DoctrineServiceRouteRepository::class),

    TucModalityRepositoryInterface::class => autowire(DoctrineTucModalityRepository::class),
    TucStatusRepositoryInterface::class => autowire(DoctrineTucStatusRepository::class),
    TucProcedureRepositoryInterface::class => autowire(DoctrineTucProcedureRepository::class),
    ProcedureTypeRepositoryInterface::class => autowire(DoctrineProcedureTypeRepository::class),
];