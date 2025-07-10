<?php

use itaxcix\Core\Handler\Admin\PermissionCreateUseCaseHandler;
use itaxcix\Core\Handler\Admin\PermissionDeleteUseCaseHandler;
use itaxcix\Core\Handler\Admin\PermissionListUseCaseHandler;
use itaxcix\Core\Handler\Admin\PermissionUpdateUseCaseHandler;
use itaxcix\Core\Handler\Admin\RoleCreateUseCaseHandler;
use itaxcix\Core\Handler\Admin\RoleDeleteUseCaseHandler;
use itaxcix\Core\Handler\Admin\RoleListUseCaseHandler;
use itaxcix\Core\Handler\Admin\RolePermissionCreateUseCaseHandler;
use itaxcix\Core\Handler\Admin\RolePermissionDeleteUseCaseHandler;
use itaxcix\Core\Handler\Admin\RolePermissionListUseCaseHandler;
use itaxcix\Core\Handler\Admin\RolePermissionUpdateUseCaseHandler;
use itaxcix\Core\Handler\Admin\RoleUpdateUseCaseHandler;
use itaxcix\Core\Handler\Admin\UserRoleCreateUseCaseHandler;
use itaxcix\Core\Handler\Admin\UserRoleDeleteUseCaseHandler;
use itaxcix\Core\Handler\Admin\UserRoleListUseCaseHandler;
use itaxcix\Core\Handler\Admin\UserRoleUpdateUseCaseHandler;
use itaxcix\Core\Handler\Admission\ApproveDriverAdmissionUseCaseHandler;
use itaxcix\Core\Handler\Admission\GetDriverDetailsUseCaseHandler;
use itaxcix\Core\Handler\Admission\GetPendingDriversUseCaseHandler;
use itaxcix\Core\Handler\Admission\RejectDriverAdmissionUseCaseHandler;
use itaxcix\Core\Handler\AuditLog\AuditLogUseCaseHandler;
use itaxcix\Core\Handler\Auth\BiometricValidationUseCaseHandler;
use itaxcix\Core\Handler\Auth\ChangePasswordUseCaseHandler;
use itaxcix\Core\Handler\Auth\DocumentValidationUseCaseHandler;
use itaxcix\Core\Handler\Auth\LoginUseCaseHandler;
use itaxcix\Core\Handler\Auth\ResendVerificationCodeUseCaseHandler;
use itaxcix\Core\Handler\Auth\StartPasswordRecoveryUseCaseHandler;
use itaxcix\Core\Handler\Auth\UserRegistrationUseCaseHandler;
use itaxcix\Core\Handler\Auth\VehicleValidationValidatorUseCaseHandler;
use itaxcix\Core\Handler\Auth\VerificationCodeUseCaseHandler;
use itaxcix\Core\Handler\Auth\VerifyRecoveryCodeUseCaseHandler;
use itaxcix\Core\Handler\Brand\BrandCreateUseCaseHandler;
use itaxcix\Core\Handler\Brand\BrandDeleteUseCaseHandler;
use itaxcix\Core\Handler\Brand\BrandListUseCaseHandler;
use itaxcix\Core\Handler\Brand\BrandUpdateUseCaseHandler;
use itaxcix\Core\Handler\Configuration\ConfigurationCreateUseCaseHandler;
use itaxcix\Core\Handler\Configuration\ConfigurationDeleteUseCaseHandler;
use itaxcix\Core\Handler\Configuration\ConfigurationListUseCaseHandler;
use itaxcix\Core\Handler\Configuration\ConfigurationUpdateUseCaseHandler;
use itaxcix\Core\Handler\ContactType\ContactTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\ContactType\ContactTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\ContactType\ContactTypeListUseCaseHandler;
use itaxcix\Core\Handler\ContactType\ContactTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\DocumentType\DocumentTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\DocumentType\DocumentTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\DocumentType\DocumentTypeListUseCaseHandler;
use itaxcix\Core\Handler\DocumentType\DocumentTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\District\DistrictCreateUseCaseHandler;
use itaxcix\Core\Handler\District\DistrictDeleteUseCaseHandler;
use itaxcix\Core\Handler\District\DistrictListUseCaseHandler;
use itaxcix\Core\Handler\District\DistrictUpdateUseCaseHandler;
use itaxcix\Core\Handler\DriverStatus\DriverStatusCreateUseCaseHandler;
use itaxcix\Core\Handler\DriverStatus\DriverStatusDeleteUseCaseHandler;
use itaxcix\Core\Handler\DriverStatus\DriverStatusListUseCaseHandler;
use itaxcix\Core\Handler\DriverStatus\DriverStatusUpdateUseCaseHandler;
use itaxcix\Core\Handler\Driver\DriverTucStatusUseCaseHandler;
use itaxcix\Core\Handler\Driver\UpdateDriverTucUseCaseHandler;
use itaxcix\Core\Handler\Emergency\EmergencyNumberGetUseCaseHandler;
use itaxcix\Core\Handler\Emergency\EmergencyNumberSaveUseCaseHandler;
use itaxcix\Core\Handler\Incident\RegisterIncidentUseCaseHandler;
use itaxcix\Core\Handler\IncidentReport\IncidentReportUseCaseHandler;
use itaxcix\Core\Handler\IncidentType\IncidentTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\IncidentType\IncidentTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\IncidentType\IncidentTypeListUseCaseHandler;
use itaxcix\Core\Handler\IncidentType\IncidentTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\InfractionReport\InfractionReportUseCaseHandler;
use itaxcix\Core\Handler\InfractionSeverity\InfractionSeverityCreateUseCaseHandler;
use itaxcix\Core\Handler\InfractionSeverity\InfractionSeverityDeleteUseCaseHandler;
use itaxcix\Core\Handler\InfractionSeverity\InfractionSeverityListUseCaseHandler;
use itaxcix\Core\Handler\InfractionSeverity\InfractionSeverityUpdateUseCaseHandler;
use itaxcix\Core\Handler\InfractionStatus\InfractionStatusCreateUseCaseHandler;
use itaxcix\Core\Handler\InfractionStatus\InfractionStatusDeleteUseCaseHandler;
use itaxcix\Core\Handler\InfractionStatus\InfractionStatusListUseCaseHandler;
use itaxcix\Core\Handler\InfractionStatus\InfractionStatusUpdateUseCaseHandler;
use itaxcix\Core\Handler\ProcedureType\ProcedureTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\ProcedureType\ProcedureTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\ProcedureType\ProcedureTypeListUseCaseHandler;
use itaxcix\Core\Handler\ProcedureType\ProcedureTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\Profile\ChangeEmailUseCaseHandler;
use itaxcix\Core\Handler\Profile\ChangePhoneUseCaseHandler;
use itaxcix\Core\Handler\Profile\GetAdminProfileUseCaseHandler;
use itaxcix\Core\Handler\Profile\GetCitizenProfileUseCaseHandler;
use itaxcix\Core\Handler\Profile\GetDriverProfileUseCaseHandler;
use itaxcix\Core\Handler\Profile\VerifyEmailChangeUseCaseHandler;
use itaxcix\Core\Handler\Profile\VerifyPhoneChangeUseCaseHandler;
use itaxcix\Core\Handler\ServiceType\ServiceTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\ServiceType\ServiceTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\ServiceType\ServiceTypeListUseCaseHandler;
use itaxcix\Core\Handler\ServiceType\ServiceTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\Travel\CancelTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\CompleteTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\GetTravelHistoryUseCaseHandler;
use itaxcix\Core\Handler\Travel\GetTravelRatingsByTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\RateTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\RequestNewTravelUseCaseHandler;
use itaxcix\Core\Handler\Travel\RespondToTravelRequestUseCaseHandler;
use itaxcix\Core\Handler\Travel\StartAcceptedTravelUseCaseHandler;
use itaxcix\Core\Handler\Rating\GetUserRatingsCommentsUseCaseHandler;
use itaxcix\Core\Handler\TravelReport\TravelReportUseCaseHandler;
use itaxcix\Core\Handler\TravelStatus\TravelStatusCreateUseCaseHandler;
use itaxcix\Core\Handler\TravelStatus\TravelStatusDeleteUseCaseHandler;
use itaxcix\Core\Handler\TravelStatus\TravelStatusListUseCaseHandler;
use itaxcix\Core\Handler\TravelStatus\TravelStatusUpdateUseCaseHandler;
use itaxcix\Core\Handler\TucModality\TucModalityCreateUseCaseHandler;
use itaxcix\Core\Handler\TucModality\TucModalityDeleteUseCaseHandler;
use itaxcix\Core\Handler\TucModality\TucModalityListUseCaseHandler;
use itaxcix\Core\Handler\TucModality\TucModalityUpdateUseCaseHandler;
use itaxcix\Core\Handler\TucStatus\TucStatusCreateUseCaseHandler;
use itaxcix\Core\Handler\TucStatus\TucStatusDeleteUseCaseHandler;
use itaxcix\Core\Handler\TucStatus\TucStatusListUseCaseHandler;
use itaxcix\Core\Handler\TucStatus\TucStatusUpdateUseCaseHandler;
use itaxcix\Core\Handler\User\CitizenToDriverUseCaseHandler;
use itaxcix\Core\Handler\User\DriverToCitizenUseCaseHandler;
use itaxcix\Core\Handler\UserCodeType\UserCodeTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\UserCodeType\UserCodeTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\UserCodeType\UserCodeTypeListUseCaseHandler;
use itaxcix\Core\Handler\UserCodeType\UserCodeTypeUpdateUseCaseHandler;
use itaxcix\Core\Handler\UserReport\UserReportUseCaseHandler;
use itaxcix\Core\Handler\UserStatus\UserStatusCreateUseCaseHandler;
use itaxcix\Core\Handler\UserStatus\UserStatusDeleteUseCaseHandler;
use itaxcix\Core\Handler\UserStatus\UserStatusListUseCaseHandler;
use itaxcix\Core\Handler\UserStatus\UserStatusUpdateUseCaseHandler;
use itaxcix\Core\Handler\Vehicle\AssociateUserVehicleUseCaseHandler;
use itaxcix\Core\Handler\Vehicle\DisassociateUserVehicleUseCaseHandler;
use itaxcix\Core\Handler\User\UserProfilePhotoUploadUseCaseHandler;
use itaxcix\Core\Handler\User\UserProfilePhotoUseCaseHandler;
use itaxcix\Core\Handler\VehicleClass\VehicleClassCreateUseCaseHandler;
use itaxcix\Core\Handler\VehicleClass\VehicleClassDeleteUseCaseHandler;
use itaxcix\Core\Handler\VehicleClass\VehicleClassListUseCaseHandler;
use itaxcix\Core\Handler\VehicleClass\VehicleClassUpdateUseCaseHandler;
use itaxcix\Core\Handler\VehicleReport\VehicleReportUseCaseHandler;
use itaxcix\Core\UseCases\Admin\Dashboard\GetDashboardStatsUseCase;
use itaxcix\Core\UseCases\Admin\Permission\CreatePermissionUseCase;
use itaxcix\Core\UseCases\Admin\Permission\DeletePermissionUseCase;
use itaxcix\Core\UseCases\Admin\Permission\GetPermissionUseCase;
use itaxcix\Core\UseCases\Admin\Permission\ListPermissionsUseCase;
use itaxcix\Core\UseCases\Admin\Permission\UpdatePermissionUseCase;
use itaxcix\Core\UseCases\Admin\PermissionCreateUseCase;
use itaxcix\Core\UseCases\Admin\PermissionDeleteUseCase;
use itaxcix\Core\UseCases\Admin\PermissionListUseCase;
use itaxcix\Core\UseCases\Admin\PermissionUpdateUseCase;
use itaxcix\Core\UseCases\Admin\Role\AssignPermissionsToRoleUseCase;
use itaxcix\Core\UseCases\Admin\Role\CreateRoleUseCase;
use itaxcix\Core\UseCases\Admin\Role\DeleteRoleUseCase;
use itaxcix\Core\UseCases\Admin\Role\GetRoleWithPermissionsUseCase;
use itaxcix\Core\UseCases\Admin\Role\ListRolesUseCase;
use itaxcix\Core\UseCases\Admin\Role\UpdateRoleUseCase;
use itaxcix\Core\UseCases\Admin\RoleCreateUseCase;
use itaxcix\Core\UseCases\Admin\RoleDeleteUseCase;
use itaxcix\Core\UseCases\Admin\RoleListUseCase;
use itaxcix\Core\UseCases\Admin\RolePermissionCreateUseCase;
use itaxcix\Core\UseCases\Admin\RolePermissionDeleteUseCase;
use itaxcix\Core\UseCases\Admin\RolePermissionListUseCase;
use itaxcix\Core\UseCases\Admin\RolePermissionUpdateUseCase;
use itaxcix\Core\UseCases\Admin\RoleUpdateUseCase;
use itaxcix\Core\UseCases\Admin\User\AdminUserListUseCase;
use itaxcix\Core\UseCases\Admin\User\AssignRolesToUserUseCase;
use itaxcix\Core\UseCases\Admin\User\ChangeUserStatusUseCase;
use itaxcix\Core\UseCases\Admin\User\ForceVerifyContactUseCase;
use itaxcix\Core\UseCases\Admin\User\GetUserDetailUseCase;
use itaxcix\Core\UseCases\Admin\User\GetUserWithRolesUseCase;
use itaxcix\Core\UseCases\Admin\User\ListUsersUseCase;
use itaxcix\Core\UseCases\Admin\User\ResetUserPasswordUseCase;
use itaxcix\Core\UseCases\Admin\User\UpdateUserRolesUseCase;
use itaxcix\Core\UseCases\Admin\User\CreateAdminUserUseCase;
use itaxcix\Core\UseCases\Admin\UserRoleCreateUseCase;
use itaxcix\Core\UseCases\Admin\UserRoleDeleteUseCase;
use itaxcix\Core\UseCases\Admin\UserRoleListUseCase;
use itaxcix\Core\UseCases\Admin\UserRoleUpdateUseCase;
use itaxcix\Core\UseCases\Admission\ApproveDriverAdmissionUseCase;
use itaxcix\Core\UseCases\Admission\GetDriverDetailsUseCase;
use itaxcix\Core\UseCases\Admission\GetPendingDriversUseCase;
use itaxcix\Core\UseCases\Admission\RejectDriverAdmissionUseCase;
use itaxcix\Core\UseCases\AuditLog\AuditLogUseCase;
use itaxcix\Core\UseCases\Auth\BiometricValidationUseCase;
use itaxcix\Core\UseCases\Auth\ChangePasswordUseCase;
use itaxcix\Core\UseCases\Auth\DocumentValidationUseCase;
use itaxcix\Core\UseCases\Auth\LoginUseCase;
use itaxcix\Core\UseCases\Auth\ResendVerificationCodeUseCase;
use itaxcix\Core\UseCases\Auth\StartPasswordRecoveryUseCase;
use itaxcix\Core\UseCases\Auth\UserRegistrationUseCase;
use itaxcix\Core\UseCases\Auth\VehicleValidationValidatorUseCase;
use itaxcix\Core\UseCases\Auth\VerificationCodeUseCase;
use itaxcix\Core\UseCases\Auth\VerifyRecoveryCodeUseCase;
use itaxcix\Core\UseCases\Brand\BrandCreateUseCase;
use itaxcix\Core\UseCases\Brand\BrandDeleteUseCase;
use itaxcix\Core\UseCases\Brand\BrandListUseCase;
use itaxcix\Core\UseCases\Brand\BrandUpdateUseCase;
use itaxcix\Core\UseCases\Configuration\ConfigurationCreateUseCase;
use itaxcix\Core\UseCases\Configuration\ConfigurationDeleteUseCase;
use itaxcix\Core\UseCases\Configuration\ConfigurationListUseCase;
use itaxcix\Core\UseCases\Configuration\ConfigurationUpdateUseCase;
use itaxcix\Core\UseCases\ContactType\ContactTypeCreateUseCase;
use itaxcix\Core\UseCases\ContactType\ContactTypeDeleteUseCase;
use itaxcix\Core\UseCases\ContactType\ContactTypeListUseCase;
use itaxcix\Core\UseCases\ContactType\ContactTypeUpdateUseCase;
use itaxcix\Core\UseCases\DocumentType\DocumentTypeCreateUseCase;
use itaxcix\Core\UseCases\DocumentType\DocumentTypeDeleteUseCase;
use itaxcix\Core\UseCases\DocumentType\DocumentTypeListUseCase;
use itaxcix\Core\UseCases\DocumentType\DocumentTypeUpdateUseCase;
use itaxcix\Core\UseCases\District\DistrictCreateUseCase;
use itaxcix\Core\UseCases\District\DistrictDeleteUseCase;
use itaxcix\Core\UseCases\District\DistrictListUseCase;
use itaxcix\Core\UseCases\District\DistrictUpdateUseCase;
use itaxcix\Core\UseCases\Driver\DriverTucStatusUseCase;
use itaxcix\Core\UseCases\Driver\UpdateDriverTucUseCase;
use itaxcix\Core\UseCases\DriverStatus\DriverStatusCreateUseCase;
use itaxcix\Core\UseCases\DriverStatus\DriverStatusDeleteUseCase;
use itaxcix\Core\UseCases\DriverStatus\DriverStatusListUseCase;
use itaxcix\Core\UseCases\DriverStatus\DriverStatusUpdateUseCase;
use itaxcix\Core\UseCases\Emergency\EmergencyNumberGetUseCase;
use itaxcix\Core\UseCases\Emergency\EmergencyNumberSaveUseCase;
use itaxcix\Core\UseCases\Incident\RegisterIncidentUseCase;
use itaxcix\Core\UseCases\Incident\GetUserIncidentsUseCase;
use itaxcix\Core\UseCases\IncidentReport\IncidentReportUseCase;
use itaxcix\Core\UseCases\IncidentType\IncidentTypeCreateUseCase;
use itaxcix\Core\UseCases\IncidentType\IncidentTypeDeleteUseCase;
use itaxcix\Core\UseCases\IncidentType\IncidentTypeListUseCase;
use itaxcix\Core\UseCases\IncidentType\IncidentTypeUpdateUseCase;
use itaxcix\Core\UseCases\InfractionReport\InfractionReportUseCase;
use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityCreateUseCase;
use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityDeleteUseCase;
use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityListUseCase;
use itaxcix\Core\UseCases\InfractionSeverity\InfractionSeverityUpdateUseCase;
use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusCreateUseCase;
use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusDeleteUseCase;
use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusListUseCase;
use itaxcix\Core\UseCases\InfractionStatus\InfractionStatusUpdateUseCase;
use itaxcix\Core\UseCases\ProcedureType\ProcedureTypeCreateUseCase;
use itaxcix\Core\UseCases\ProcedureType\ProcedureTypeDeleteUseCase;
use itaxcix\Core\UseCases\ProcedureType\ProcedureTypeListUseCase;
use itaxcix\Core\UseCases\ProcedureType\ProcedureTypeUpdateUseCase;
use itaxcix\Core\UseCases\Profile\ChangeEmailUseCase;
use itaxcix\Core\UseCases\Profile\ChangePhoneUseCase;
use itaxcix\Core\UseCases\Profile\GetAdminProfileUseCase;
use itaxcix\Core\UseCases\Profile\GetCitizenProfileUseCase;
use itaxcix\Core\UseCases\Profile\GetDriverProfileUseCase;
use itaxcix\Core\UseCases\Profile\VerifyEmailChangeUseCase;
use itaxcix\Core\UseCases\Profile\VerifyPhoneChangeUseCase;
use itaxcix\Core\UseCases\ServiceType\ServiceTypeCreateUseCase;
use itaxcix\Core\UseCases\ServiceType\ServiceTypeDeleteUseCase;
use itaxcix\Core\UseCases\ServiceType\ServiceTypeListUseCase;
use itaxcix\Core\UseCases\ServiceType\ServiceTypeUpdateUseCase;
use itaxcix\Core\UseCases\Travel\CancelTravelUseCase;
use itaxcix\Core\UseCases\Travel\CompleteTravelUseCase;
use itaxcix\Core\UseCases\Travel\GetTravelHistoryUseCase;
use itaxcix\Core\UseCases\Travel\GetTravelRatingsByTravelUseCase;
use itaxcix\Core\UseCases\Travel\RateTravelUseCase;
use itaxcix\Core\UseCases\Travel\RequestNewTravelUseCase;
use itaxcix\Core\UseCases\Travel\RespondToTravelRequestUseCase;
use itaxcix\Core\UseCases\Travel\StartAcceptedTravelUseCase;
use itaxcix\Core\UseCases\Rating\GetUserRatingsCommentsUseCase;
use itaxcix\Core\UseCases\TravelReport\TravelReportUseCase;
use itaxcix\Core\UseCases\TravelStatus\TravelStatusCreateUseCase;
use itaxcix\Core\UseCases\TravelStatus\TravelStatusDeleteUseCase;
use itaxcix\Core\UseCases\TravelStatus\TravelStatusListUseCase;
use itaxcix\Core\UseCases\TravelStatus\TravelStatusUpdateUseCase;
use itaxcix\Core\UseCases\TucModality\TucModalityCreateUseCase;
use itaxcix\Core\UseCases\TucModality\TucModalityDeleteUseCase;
use itaxcix\Core\UseCases\TucModality\TucModalityListUseCase;
use itaxcix\Core\UseCases\TucModality\TucModalityUpdateUseCase;
use itaxcix\Core\UseCases\TucStatus\TucStatusCreateUseCase;
use itaxcix\Core\UseCases\TucStatus\TucStatusDeleteUseCase;
use itaxcix\Core\UseCases\TucStatus\TucStatusListUseCase;
use itaxcix\Core\UseCases\TucStatus\TucStatusUpdateUseCase;
use itaxcix\Core\UseCases\User\CitizenToDriverUseCase;
use itaxcix\Core\UseCases\User\DriverToCitizenUseCase;
use itaxcix\Core\Handler\HelpCenter\HelpCenterCreateUseCaseHandler;
use itaxcix\Core\Handler\HelpCenter\HelpCenterDeleteUseCaseHandler;
use itaxcix\Core\Handler\HelpCenter\HelpCenterListUseCaseHandler;
use itaxcix\Core\Handler\HelpCenter\HelpCenterPublicListUseCaseHandler;
use itaxcix\Core\Handler\HelpCenter\HelpCenterUpdateUseCaseHandler;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterCreateUseCase;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterDeleteUseCase;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterListUseCase;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterPublicListUseCase;
use itaxcix\Core\UseCases\HelpCenter\HelpCenterUpdateUseCase;
use itaxcix\Core\Handler\Company\CompanyCreateUseCaseHandler;
use itaxcix\Core\Handler\Company\CompanyDeleteUseCaseHandler;
use itaxcix\Core\Handler\Company\CompanyListUseCaseHandler;
use itaxcix\Core\Handler\Company\CompanyUpdateUseCaseHandler;
use itaxcix\Core\UseCases\Company\CompanyCreateUseCase;
use itaxcix\Core\UseCases\Company\CompanyDeleteUseCase;
use itaxcix\Core\UseCases\Company\CompanyListUseCase;
use itaxcix\Core\UseCases\Company\CompanyUpdateUseCase;
use itaxcix\Core\UseCases\User\UserProfilePhotoUploadUseCase;
use itaxcix\Core\UseCases\User\UserProfilePhotoUseCase;
use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeCreateUseCase;
use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeDeleteUseCase;
use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeUpdateUseCase;
use itaxcix\Core\UseCases\UserCodeType\UserCodeTypeListUseCase;
use itaxcix\Core\UseCases\UserReport\UserReportUseCase;
use itaxcix\Core\UseCases\UserStatus\UserStatusCreateUseCase;
use itaxcix\Core\UseCases\UserStatus\UserStatusDeleteUseCase;
use itaxcix\Core\UseCases\UserStatus\UserStatusListUseCase;
use itaxcix\Core\UseCases\UserStatus\UserStatusUpdateUseCase;
use itaxcix\Core\Handler\Department\DepartmentCreateUseCaseHandler;
use itaxcix\Core\Handler\Department\DepartmentDeleteUseCaseHandler;
use itaxcix\Core\Handler\Department\DepartmentListUseCaseHandler;
use itaxcix\Core\Handler\Department\DepartmentUpdateUseCaseHandler;
use itaxcix\Core\UseCases\Department\DepartmentCreateUseCase;
use itaxcix\Core\UseCases\Department\DepartmentDeleteUseCase;
use itaxcix\Core\UseCases\Department\DepartmentListUseCase;
use itaxcix\Core\UseCases\Department\DepartmentUpdateUseCase;
use itaxcix\Core\UseCases\Vehicle\AssociateUserVehicleUseCase;
use itaxcix\Core\UseCases\Vehicle\DisassociateUserVehicleUseCase;
use itaxcix\Core\UseCases\VehicleClass\VehicleClassCreateUseCase;
use itaxcix\Core\UseCases\VehicleClass\VehicleClassDeleteUseCase;
use itaxcix\Core\UseCases\VehicleClass\VehicleClassListUseCase;
use itaxcix\Core\UseCases\VehicleClass\VehicleClassUpdateUseCase;
use itaxcix\Core\UseCases\VehicleReport\VehicleReportUseCase;
use itaxcix\Shared\Validators\Admin\User\CreateAdminUserValidator;
use itaxcix\Shared\Validators\useCases\AuditLog\AuditLogValidator;
use itaxcix\Shared\Validators\useCases\Brand\BrandValidator;
use itaxcix\Shared\Validators\useCases\Department\DepartmentValidator;
use itaxcix\Shared\Validators\useCases\Company\CompanyValidator;
use itaxcix\Core\Handler\Province\ProvinceCreateUseCaseHandler;
use itaxcix\Core\Handler\Province\ProvinceDeleteUseCaseHandler;
use itaxcix\Core\Handler\Province\ProvinceListUseCaseHandler;
use itaxcix\Core\Handler\Province\ProvinceUpdateUseCaseHandler;
use itaxcix\Core\UseCases\Province\ProvinceCreateUseCase;
use itaxcix\Core\UseCases\Province\ProvinceDeleteUseCase;
use itaxcix\Core\UseCases\Province\ProvinceListUseCase;
use itaxcix\Core\UseCases\Province\ProvinceUpdateUseCase;
use itaxcix\Shared\Validators\useCases\IncidentReport\IncidentReportValidator;
use itaxcix\Shared\Validators\useCases\IncidentType\IncidentTypeValidator;
use itaxcix\Shared\Validators\useCases\InfractionReport\InfractionReportValidator;
use itaxcix\Shared\Validators\useCases\InfractionSeverity\InfractionSeverityValidator;
use itaxcix\Shared\Validators\useCases\InfractionStatus\InfractionStatusValidator;
use itaxcix\Shared\Validators\useCases\ProcedureType\ProcedureTypeValidator;
use itaxcix\Shared\Validators\useCases\Province\ProvinceValidator;
use itaxcix\Shared\Validators\useCases\District\DistrictValidator;
use itaxcix\Shared\Validators\useCases\DocumentType\DocumentTypeValidator;
use itaxcix\Core\Handler\Model\ModelCreateUseCaseHandler;
use itaxcix\Core\Handler\Model\ModelDeleteUseCaseHandler;
use itaxcix\Core\Handler\Model\ModelListUseCaseHandler;
use itaxcix\Core\Handler\Model\ModelUpdateUseCaseHandler;
use itaxcix\Core\UseCases\Model\ModelCreateUseCase;
use itaxcix\Core\UseCases\Model\ModelDeleteUseCase;
use itaxcix\Core\UseCases\Model\ModelListUseCase;
use itaxcix\Core\UseCases\Model\ModelUpdateUseCase;
use itaxcix\Shared\Validators\useCases\Model\ModelValidator;

// Color imports
use itaxcix\Core\Handler\Color\ColorCreateUseCaseHandler;
use itaxcix\Core\Handler\Color\ColorDeleteUseCaseHandler;
use itaxcix\Core\Handler\Color\ColorListUseCaseHandler;
use itaxcix\Core\Handler\Color\ColorUpdateUseCaseHandler;
use itaxcix\Core\UseCases\Color\ColorCreateUseCase;
use itaxcix\Core\UseCases\Color\ColorDeleteUseCase;
use itaxcix\Core\UseCases\Color\ColorListUseCase;
use itaxcix\Core\UseCases\Color\ColorUpdateUseCase;
use itaxcix\Shared\Validators\useCases\Color\ColorValidator;

// FuelType imports
use itaxcix\Core\Handler\FuelType\FuelTypeCreateUseCaseHandler;
use itaxcix\Core\Handler\FuelType\FuelTypeDeleteUseCaseHandler;
use itaxcix\Core\Handler\FuelType\FuelTypeListUseCaseHandler;
use itaxcix\Core\Handler\FuelType\FuelTypeUpdateUseCaseHandler;
use itaxcix\Core\UseCases\FuelType\FuelTypeCreateUseCase;
use itaxcix\Core\UseCases\FuelType\FuelTypeDeleteUseCase;
use itaxcix\Core\UseCases\FuelType\FuelTypeListUseCase;
use itaxcix\Core\UseCases\FuelType\FuelTypeUpdateUseCase;
use itaxcix\Shared\Validators\useCases\FuelType\FuelTypeValidator;

// Category imports
use itaxcix\Core\Handler\Category\CategoryCreateUseCaseHandler;
use itaxcix\Core\Handler\Category\CategoryDeleteUseCaseHandler;
use itaxcix\Core\Handler\Category\CategoryListUseCaseHandler;
use itaxcix\Core\Handler\Category\CategoryUpdateUseCaseHandler;
use itaxcix\Core\UseCases\Category\CategoryCreateUseCase;
use itaxcix\Core\UseCases\Category\CategoryDeleteUseCase;
use itaxcix\Core\UseCases\Category\CategoryListUseCase;
use itaxcix\Core\UseCases\Category\CategoryUpdateUseCase;
use itaxcix\Shared\Validators\useCases\Category\CategoryValidator;

use itaxcix\Shared\Validators\useCases\ServiceType\ServiceTypeValidator;
use itaxcix\Shared\Validators\useCases\TravelReport\TravelReportValidator;
use itaxcix\Shared\Validators\useCases\TravelStatus\TravelStatusValidator;
use itaxcix\Shared\Validators\useCases\TucModality\TucModalityValidator;
use itaxcix\Shared\Validators\useCases\UserCodeType\UserCodeTypeValidator;
use itaxcix\Shared\Validators\useCases\UserReport\UserReportValidator;
use itaxcix\Shared\Validators\useCases\VehicleClass\VehicleClassValidator;
use itaxcix\Shared\Validators\useCases\VehicleReport\VehicleReportValidator;
use function DI\autowire;

return array(
    // Auth Use Cases
    LoginUseCase::class => autowire(LoginUseCaseHandler::class),
    DocumentValidationUseCase::class => autowire(DocumentValidationUseCaseHandler::class),
    VehicleValidationValidatorUseCase::class => autowire(VehicleValidationValidatorUseCaseHandler::class),
    BiometricValidationUseCase::class => autowire(BiometricValidationUseCaseHandler::class),
    UserRegistrationUseCase::class => autowire(UserRegistrationUseCaseHandler::class),
    VerificationCodeUseCase::class => autowire(VerificationCodeUseCaseHandler::class),
    ChangePasswordUseCase::class => autowire(ChangePasswordUseCaseHandler::class),
    StartPasswordRecoveryUseCase::class => autowire(StartPasswordRecoveryUseCaseHandler::class),
    VerifyRecoveryCodeUseCase::class => autowire(VerifyRecoveryCodeUseCaseHandler::class),
    ResendVerificationCodeUseCase::class => autowire(ResendVerificationCodeUseCaseHandler::class),

    // Admission Use Cases
    ApproveDriverAdmissionUseCase::class => autowire(ApproveDriverAdmissionUseCaseHandler::class),
    GetDriverDetailsUseCase::class => autowire(GetDriverDetailsUseCaseHandler::class),
    GetPendingDriversUseCase::class => autowire(GetPendingDriversUseCaseHandler::class),
    RejectDriverAdmissionUseCase::class => autowire(RejectDriverAdmissionUseCaseHandler::class),

    // Driver Use Cases
    DriverTucStatusUseCase::class => autowire(DriverTucStatusUseCaseHandler::class),
    UpdateDriverTucUseCase::class => autowire(UpdateDriverTucUseCaseHandler::class),

    // User Use Cases
    UserProfilePhotoUploadUseCase::class => autowire(UserProfilePhotoUploadUseCaseHandler::class),
    UserProfilePhotoUseCase::class => autowire(UserProfilePhotoUseCaseHandler::class),

    // Profile Use Cases
    GetAdminProfileUseCase::class => autowire(GetAdminProfileUseCaseHandler::class),
    GetDriverProfileUseCase::class => autowire(GetDriverProfileUseCaseHandler::class),
    GetCitizenProfileUseCase::class => autowire(GetCitizenProfileUseCaseHandler::class),
    ChangeEmailUseCase::class => autowire(ChangeEmailUseCaseHandler::class),
    ChangePhoneUseCase::class => autowire(ChangePhoneUseCaseHandler::class),
    VerifyEmailChangeUseCase::class => autowire(VerifyEmailChangeUseCaseHandler::class),
    VerifyPhoneChangeUseCase::class => autowire(VerifyPhoneChangeUseCaseHandler::class),

    // Travel Use Cases
    CancelTravelUseCase::class => autowire(CancelTravelUseCaseHandler::class),
    CompleteTravelUseCase::class => autowire(CompleteTravelUseCaseHandler::class),
    RequestNewTravelUseCase::class => autowire(RequestNewTravelUseCaseHandler::class),
    StartAcceptedTravelUseCase::class => autowire(StartAcceptedTravelUseCaseHandler::class),
    RespondToTravelRequestUseCase::class => autowire(RespondToTravelRequestUseCaseHandler::class),
    GetTravelHistoryUseCase::class => autowire(GetTravelHistoryUseCaseHandler::class),
    RateTravelUseCase::class => autowire(RateTravelUseCaseHandler::class),
    GetTravelRatingsByTravelUseCase::class => autowire(GetTravelRatingsByTravelUseCaseHandler::class),

    // Rating Use Cases
    GetUserRatingsCommentsUseCase::class => autowire(GetUserRatingsCommentsUseCaseHandler::class),

    // Vehicle Use Cases
    AssociateUserVehicleUseCase::class => autowire(AssociateUserVehicleUseCaseHandler::class),
    DisassociateUserVehicleUseCase::class => autowire(DisassociateUserVehicleUseCaseHandler::class),

    // Emergency Use Cases
    EmergencyNumberGetUseCase::class => autowire(EmergencyNumberGetUseCaseHandler::class),
    EmergencyNumberSaveUseCase::class => autowire(EmergencyNumberSaveUseCaseHandler::class),

    // Incident Use Cases
    RegisterIncidentUseCase::class => autowire(RegisterIncidentUseCaseHandler::class),
    GetUserIncidentsUseCase::class => autowire(),

    // Admin Use Cases
    PermissionCreateUseCase::class => autowire(PermissionCreateUseCaseHandler::class),
    PermissionDeleteUseCase::class => autowire(PermissionDeleteUseCaseHandler::class),
    PermissionListUseCase::class => autowire(PermissionListUseCaseHandler::class),
    PermissionUpdateUseCase::class => autowire(PermissionUpdateUseCaseHandler::class),

    RoleCreateUseCase::class => autowire(RoleCreateUseCaseHandler::class),
    RoleDeleteUseCase::class => autowire(RoleDeleteUseCaseHandler::class),
    RoleListUseCase::class => autowire(RoleListUseCaseHandler::class),
    RoleUpdateUseCase::class => autowire(RoleUpdateUseCaseHandler::class),

    RolePermissionCreateUseCase::class => autowire(RolePermissionCreateUseCaseHandler::class),
    RolePermissionDeleteUseCase::class => autowire(RolePermissionDeleteUseCaseHandler::class),
    RolePermissionListUseCase::class => autowire(RolePermissionListUseCaseHandler::class),
    RolePermissionUpdateUseCase::class => autowire(RolePermissionUpdateUseCaseHandler::class),

    UserRoleCreateUseCase::class => autowire(UserRoleCreateUseCaseHandler::class),
    UserRoleDeleteUseCase::class => autowire(UserRoleDeleteUseCaseHandler::class),
    UserRoleListUseCase::class => autowire(UserRoleListUseCaseHandler::class),
    UserRoleUpdateUseCase::class => autowire(UserRoleUpdateUseCaseHandler::class),

    // HelpCenter Use Cases
    HelpCenterListUseCase::class => autowire(HelpCenterListUseCaseHandler::class),
    HelpCenterPublicListUseCase::class => autowire(HelpCenterPublicListUseCaseHandler::class),
    HelpCenterCreateUseCase::class => autowire(HelpCenterCreateUseCaseHandler::class),
    HelpCenterUpdateUseCase::class => autowire(HelpCenterUpdateUseCaseHandler::class),
    HelpCenterDeleteUseCase::class => autowire(HelpCenterDeleteUseCaseHandler::class),

    // Company Use Cases
    CompanyListUseCase::class => autowire(CompanyListUseCase::class),
    CompanyCreateUseCase::class => autowire(CompanyCreateUseCase::class),
    CompanyUpdateUseCase::class => autowire(CompanyUpdateUseCase::class),
    CompanyDeleteUseCase::class => autowire(CompanyDeleteUseCase::class),

    CompanyListUseCaseHandler::class => autowire(CompanyListUseCaseHandler::class),
    CompanyCreateUseCaseHandler::class => autowire(CompanyCreateUseCaseHandler::class),
    CompanyUpdateUseCaseHandler::class => autowire(CompanyUpdateUseCaseHandler::class),
    CompanyDeleteUseCaseHandler::class => autowire(CompanyDeleteUseCaseHandler::class),

    CompanyValidator::class => autowire(CompanyValidator::class),

    // Configuration Use Cases
    ConfigurationListUseCase::class => autowire(ConfigurationListUseCase::class),
    ConfigurationCreateUseCase::class => autowire(ConfigurationCreateUseCase::class),
    ConfigurationUpdateUseCase::class => autowire(ConfigurationUpdateUseCase::class),
    ConfigurationDeleteUseCase::class => autowire(ConfigurationDeleteUseCase::class),

    ConfigurationListUseCaseHandler::class => autowire(ConfigurationListUseCaseHandler::class),
    ConfigurationCreateUseCaseHandler::class => autowire(ConfigurationCreateUseCaseHandler::class),
    ConfigurationUpdateUseCaseHandler::class => autowire(ConfigurationUpdateUseCaseHandler::class),
    ConfigurationDeleteUseCaseHandler::class => autowire(ConfigurationDeleteUseCaseHandler::class),

    // ContactType Use Cases
    ContactTypeListUseCase::class => autowire(ContactTypeListUseCase::class),
    ContactTypeCreateUseCase::class => autowire(ContactTypeCreateUseCase::class),
    ContactTypeUpdateUseCase::class => autowire(ContactTypeUpdateUseCase::class),
    ContactTypeDeleteUseCase::class => autowire(ContactTypeDeleteUseCase::class),

    ContactTypeCreateUseCaseHandler::class => autowire(ContactTypeCreateUseCaseHandler::class),
    ContactTypeUpdateUseCaseHandler::class => autowire(ContactTypeUpdateUseCaseHandler::class),
    ContactTypeDeleteUseCaseHandler::class => autowire(ContactTypeDeleteUseCaseHandler::class),
    ContactTypeListUseCaseHandler::class => autowire(ContactTypeListUseCaseHandler::class),

    // DocumentType Use Cases
    DocumentTypeListUseCase::class => autowire(DocumentTypeListUseCase::class),
    DocumentTypeCreateUseCase::class => autowire(DocumentTypeCreateUseCase::class),
    DocumentTypeUpdateUseCase::class => autowire(DocumentTypeUpdateUseCase::class),
    DocumentTypeDeleteUseCase::class => autowire(DocumentTypeDeleteUseCase::class),

    DocumentTypeCreateUseCaseHandler::class => autowire(DocumentTypeCreateUseCaseHandler::class),
    DocumentTypeUpdateUseCaseHandler::class => autowire(DocumentTypeUpdateUseCaseHandler::class),
    DocumentTypeListUseCaseHandler::class => autowire(DocumentTypeListUseCaseHandler::class),
    DocumentTypeDeleteUseCaseHandler::class => autowire(DocumentTypeDeleteUseCaseHandler::class),

    DocumentTypeValidator::class => autowire(DocumentTypeValidator::class),

    // User Transition Use Cases
    CitizenToDriverUseCase::class => autowire(CitizenToDriverUseCaseHandler::class),
    DriverToCitizenUseCase::class => autowire(DriverToCitizenUseCaseHandler::class),

    // Nuevos casos de uso para administración avanzada (inyección directa)
    ListRolesUseCase::class => autowire(),
    CreateRoleUseCase::class => autowire(),
    UpdateRoleUseCase::class => autowire(),
    DeleteRoleUseCase::class => autowire(),
    AssignPermissionsToRoleUseCase::class => autowire(),
    GetRoleWithPermissionsUseCase::class => autowire(),
    ListUsersUseCase::class => autowire(),
    AssignRolesToUserUseCase::class => autowire(),
    GetUserWithRolesUseCase::class => autowire(),
    ListPermissionsUseCase::class => autowire(),
    CreatePermissionUseCase::class => autowire(),
    GetPermissionUseCase::class => autowire(),
    UpdatePermissionUseCase::class => autowire(),
    DeletePermissionUseCase::class => autowire(),
    GetDashboardStatsUseCase::class => autowire(),

    // Nuevos casos de uso para gestión administrativa avanzada de usuarios
    AdminUserListUseCase::class => autowire(),
    GetUserDetailUseCase::class => autowire(),
    ChangeUserStatusUseCase::class => autowire(),
    ForceVerifyContactUseCase::class => autowire(),
    ResetUserPasswordUseCase::class => autowire(),
    UpdateUserRolesUseCase::class => autowire(),
    CreateAdminUserUseCase::class => autowire(),

    // UserStatus Use Cases
    UserStatusListUseCase::class => autowire(UserStatusListUseCase::class),
    UserStatusCreateUseCase::class => autowire(UserStatusCreateUseCase::class),
    UserStatusUpdateUseCase::class => autowire(UserStatusUpdateUseCase::class),
    UserStatusDeleteUseCase::class => autowire(UserStatusDeleteUseCase::class),

    UserStatusListUseCaseHandler::class => autowire(UserStatusListUseCaseHandler::class),
    UserStatusCreateUseCaseHandler::class => autowire(UserStatusCreateUseCaseHandler::class),
    UserStatusUpdateUseCaseHandler::class => autowire(UserStatusUpdateUseCaseHandler::class),
    UserStatusDeleteUseCaseHandler::class => autowire(UserStatusDeleteUseCaseHandler::class),

    // DriverStatus Use Cases
    DriverStatusListUseCase::class => autowire(DriverStatusListUseCase::class),
    DriverStatusCreateUseCase::class => autowire(DriverStatusCreateUseCase::class),
    DriverStatusUpdateUseCase::class => autowire(DriverStatusUpdateUseCase::class),
    DriverStatusDeleteUseCase::class => autowire(DriverStatusDeleteUseCase::class),

    DriverStatusListUseCaseHandler::class => autowire(DriverStatusListUseCaseHandler::class),
    DriverStatusCreateUseCaseHandler::class => autowire(DriverStatusCreateUseCaseHandler::class),
    DriverStatusUpdateUseCaseHandler::class => autowire(DriverStatusUpdateUseCaseHandler::class),
    DriverStatusDeleteUseCaseHandler::class => autowire(DriverStatusDeleteUseCaseHandler::class),

    // Department Use Cases
    DepartmentListUseCase::class => autowire(DepartmentListUseCase::class),
    DepartmentCreateUseCase::class => autowire(DepartmentCreateUseCase::class),
    DepartmentUpdateUseCase::class => autowire(DepartmentUpdateUseCase::class),
    DepartmentDeleteUseCase::class => autowire(DepartmentDeleteUseCase::class),

    DepartmentListUseCaseHandler::class => autowire(DepartmentListUseCaseHandler::class),
    DepartmentCreateUseCaseHandler::class => autowire(DepartmentCreateUseCaseHandler::class),
    DepartmentUpdateUseCaseHandler::class => autowire(DepartmentUpdateUseCaseHandler::class),
    DepartmentDeleteUseCaseHandler::class => autowire(DepartmentDeleteUseCaseHandler::class),

    // Department Validator
    DepartmentValidator::class => autowire(DepartmentValidator::class),

    // Province Use Cases
    ProvinceListUseCase::class => autowire(ProvinceListUseCase::class),
    ProvinceCreateUseCase::class => autowire(ProvinceCreateUseCase::class),
    ProvinceUpdateUseCase::class => autowire(ProvinceUpdateUseCase::class),
    ProvinceDeleteUseCase::class => autowire(ProvinceDeleteUseCase::class),

    ProvinceListUseCaseHandler::class => autowire(ProvinceListUseCaseHandler::class),
    ProvinceCreateUseCaseHandler::class => autowire(ProvinceCreateUseCaseHandler::class),
    ProvinceUpdateUseCaseHandler::class => autowire(ProvinceUpdateUseCaseHandler::class),
    ProvinceDeleteUseCaseHandler::class => autowire(ProvinceDeleteUseCaseHandler::class),

    // Province Validator
    ProvinceValidator::class => autowire(ProvinceValidator::class),

    // District Use Cases
    DistrictListUseCase::class => autowire(DistrictListUseCase::class),
    DistrictCreateUseCase::class => autowire(DistrictCreateUseCase::class),
    DistrictUpdateUseCase::class => autowire(DistrictUpdateUseCase::class),
    DistrictDeleteUseCase::class => autowire(DistrictDeleteUseCase::class),

    DistrictListUseCaseHandler::class => autowire(DistrictListUseCaseHandler::class),
    DistrictCreateUseCaseHandler::class => autowire(DistrictCreateUseCaseHandler::class),
    DistrictUpdateUseCaseHandler::class => autowire(DistrictUpdateUseCaseHandler::class),
    DistrictDeleteUseCaseHandler::class => autowire(DistrictDeleteUseCaseHandler::class),

    // District Validator
    DistrictValidator::class => autowire(DistrictValidator::class),

    // Brand Use Cases
    BrandListUseCase::class => autowire(BrandListUseCase::class),
    BrandCreateUseCase::class => autowire(BrandCreateUseCase::class),
    BrandUpdateUseCase::class => autowire(BrandUpdateUseCase::class),
    BrandDeleteUseCase::class => autowire(BrandDeleteUseCase::class),

    BrandListUseCaseHandler::class => autowire(BrandListUseCaseHandler::class),
    BrandCreateUseCaseHandler::class => autowire(BrandCreateUseCaseHandler::class),
    BrandUpdateUseCaseHandler::class => autowire(BrandUpdateUseCaseHandler::class),
    BrandDeleteUseCaseHandler::class => autowire(BrandDeleteUseCaseHandler::class),

    // Brand Validator
    BrandValidator::class => autowire(BrandValidator::class),

    // Model Use Cases
    ModelListUseCase::class => autowire(ModelListUseCase::class),
    ModelCreateUseCase::class => autowire(ModelCreateUseCase::class),
    ModelUpdateUseCase::class => autowire(ModelUpdateUseCase::class),
    ModelDeleteUseCase::class => autowire(ModelDeleteUseCase::class),

    ModelListUseCaseHandler::class => autowire(ModelListUseCaseHandler::class),
    ModelCreateUseCaseHandler::class => autowire(ModelCreateUseCaseHandler::class),
    ModelUpdateUseCaseHandler::class => autowire(ModelUpdateUseCaseHandler::class),
    ModelDeleteUseCaseHandler::class => autowire(ModelDeleteUseCaseHandler::class),

    // Model Validator
    ModelValidator::class => autowire(ModelValidator::class),

    // Color Use Cases
    ColorListUseCase::class => autowire(ColorListUseCase::class),
    ColorCreateUseCase::class => autowire(ColorCreateUseCase::class),
    ColorUpdateUseCase::class => autowire(ColorUpdateUseCase::class),
    ColorDeleteUseCase::class => autowire(ColorDeleteUseCase::class),

    ColorListUseCaseHandler::class => autowire(ColorListUseCaseHandler::class),
    ColorCreateUseCaseHandler::class => autowire(ColorCreateUseCaseHandler::class),
    ColorUpdateUseCaseHandler::class => autowire(ColorUpdateUseCaseHandler::class),
    ColorDeleteUseCaseHandler::class => autowire(ColorDeleteUseCaseHandler::class),

    // Color Validator
    ColorValidator::class => autowire(ColorValidator::class),

    // FuelType Use Cases
    FuelTypeListUseCase::class => autowire(FuelTypeListUseCase::class),
    FuelTypeCreateUseCase::class => autowire(FuelTypeCreateUseCase::class),
    FuelTypeUpdateUseCase::class => autowire(FuelTypeUpdateUseCase::class),
    FuelTypeDeleteUseCase::class => autowire(FuelTypeDeleteUseCase::class),

    FuelTypeListUseCaseHandler::class => autowire(FuelTypeListUseCaseHandler::class),
    FuelTypeCreateUseCaseHandler::class => autowire(FuelTypeCreateUseCaseHandler::class),
    FuelTypeUpdateUseCaseHandler::class => autowire(FuelTypeUpdateUseCaseHandler::class),
    FuelTypeDeleteUseCaseHandler::class => autowire(FuelTypeDeleteUseCaseHandler::class),

    // FuelType Validator
    FuelTypeValidator::class => autowire(FuelTypeValidator::class),

    // Category Use Cases
    CategoryListUseCase::class => autowire(CategoryListUseCase::class),
    CategoryCreateUseCase::class => autowire(CategoryCreateUseCase::class),
    CategoryUpdateUseCase::class => autowire(CategoryUpdateUseCase::class),
    CategoryDeleteUseCase::class => autowire(CategoryDeleteUseCase::class),

    CategoryListUseCaseHandler::class => autowire(CategoryListUseCaseHandler::class),
    CategoryCreateUseCaseHandler::class => autowire(CategoryCreateUseCaseHandler::class),
    CategoryUpdateUseCaseHandler::class => autowire(CategoryUpdateUseCaseHandler::class),
    CategoryDeleteUseCaseHandler::class => autowire(CategoryDeleteUseCaseHandler::class),

    // Category Validator
    CategoryValidator::class => autowire(CategoryValidator::class),

    VehicleClassListUseCase::class => autowire(VehicleClassListUseCase::class),
    VehicleClassCreateUseCase::class => autowire(VehicleClassCreateUseCase::class),
    VehicleClassUpdateUseCase::class => autowire(VehicleClassUpdateUseCase::class),
    VehicleClassDeleteUseCase::class => autowire(VehicleClassDeleteUseCase::class),

    VehicleClassListUseCaseHandler::class => autowire(VehicleClassListUseCaseHandler::class),
    VehicleClassCreateUseCaseHandler::class => autowire(VehicleClassCreateUseCaseHandler::class),
    VehicleClassUpdateUseCaseHandler::class => autowire(VehicleClassUpdateUseCaseHandler::class),
    VehicleClassDeleteUseCaseHandler::class => autowire(VehicleClassDeleteUseCaseHandler::class),

    VehicleClassValidator::class => autowire(VehicleClassValidator::class),

    ServiceTypeCreateUseCase::class => autowire(ServiceTypeCreateUseCase::class),
    ServiceTypeListUseCase::class => autowire(ServiceTypeListUseCase::class),
    ServiceTypeUpdateUseCase::class => autowire(ServiceTypeUpdateUseCase::class),
    ServiceTypeDeleteUseCase::class => autowire(ServiceTypeDeleteUseCase::class),

    ServiceTypeListUseCaseHandler::class => autowire(ServiceTypeListUseCaseHandler::class),
    ServiceTypeCreateUseCaseHandler::class => autowire(ServiceTypeCreateUseCaseHandler::class),
    ServiceTypeUpdateUseCaseHandler::class => autowire(ServiceTypeUpdateUseCaseHandler::class),
    ServiceTypeDeleteUseCaseHandler::class => autowire(ServiceTypeDeleteUseCaseHandler::class),

    ServiceTypeValidator::class => autowire(ServiceTypeValidator::class),

    IncidentTypeCreateUseCase::class => autowire(IncidentTypeCreateUseCase::class),
    IncidentTypeListUseCase::class => autowire(IncidentTypeListUseCase::class),
    IncidentTypeDeleteUseCase::class => autowire(IncidentTypeDeleteUseCase::class),
    IncidentTypeUpdateUseCase::class => autowire(IncidentTypeUpdateUseCase::class),

    IncidentTypeCreateUseCaseHandler::class => autowire(IncidentTypeCreateUseCaseHandler::class),
    IncidentTypeListUseCaseHandler::class => autowire(IncidentTypeListUseCaseHandler::class),
    IncidentTypeUpdateUseCaseHandler::class => autowire(IncidentTypeUpdateUseCaseHandler::class),
    IncidentTypeDeleteUseCaseHandler::class => autowire(IncidentTypeDeleteUseCaseHandler::class),

    IncidentTypeValidator::class => autowire(IncidentTypeValidator::class),

    InfractionSeverityCreateUseCase::class => autowire(InfractionSeverityCreateUseCase::class),
    InfractionSeverityListUseCase::class => autowire(InfractionSeverityListUseCase::class),
    InfractionSeverityUpdateUseCase::class => autowire(InfractionSeverityUpdateUseCase::class),
    InfractionSeverityDeleteUseCase::class => autowire(InfractionSeverityDeleteUseCase::class),

    InfractionSeverityCreateUseCaseHandler::class => autowire(InfractionSeverityCreateUseCaseHandler::class),
    InfractionSeverityListUseCaseHandler::class => autowire(InfractionSeverityListUseCaseHandler::class),
    InfractionSeverityUpdateUseCaseHandler::class => autowire(InfractionSeverityUpdateUseCaseHandler::class),
    InfractionSeverityDeleteUseCaseHandler::class => autowire(InfractionSeverityDeleteUseCaseHandler::class),

    InfractionSeverityValidator::class => autowire(InfractionSeverityValidator::class),

    InfractionStatusCreateUseCase::class => autowire(InfractionStatusCreateUseCase::class),
    InfractionStatusUpdateUseCase::class => autowire(InfractionStatusUpdateUseCase::class),
    InfractionStatusDeleteUseCase::class => autowire(InfractionStatusDeleteUseCase::class),
    InfractionStatusListUseCase::class => autowire(InfractionStatusListUseCase::class),

    InfractionStatusCreateUseCaseHandler::class => autowire(InfractionStatusCreateUseCaseHandler::class),
    InfractionStatusUpdateUseCaseHandler::class => autowire(InfractionStatusUpdateUseCaseHandler::class),
    InfractionStatusDeleteUseCaseHandler::class => autowire(InfractionStatusDeleteUseCaseHandler::class),
    InfractionStatusListUseCaseHandler::class => autowire(InfractionStatusListUseCaseHandler::class),

    InfractionStatusValidator::class => autowire(InfractionStatusValidator::class),

    TravelStatusCreateUseCase::class => autowire(TravelStatusCreateUseCase::class),
    TravelStatusDeleteUseCase::class => autowire(TravelStatusDeleteUseCase::class),
    TravelStatusListUseCase::class => autowire(TravelStatusListUseCase::class),
    TravelStatusUpdateUseCase::class => autowire(TravelStatusUpdateUseCase::class),

    TravelStatusCreateUseCaseHandler::class => autowire(TravelStatusCreateUseCaseHandler::class),
    TravelStatusDeleteUseCaseHandler::class => autowire(TravelStatusDeleteUseCaseHandler::class),
    TravelStatusUpdateUseCaseHandler::class => autowire(TravelStatusUpdateUseCaseHandler::class),
    TravelStatusListUseCaseHandler::class => autowire(TravelStatusListUseCaseHandler::class),

    TravelStatusValidator::class => autowire(TravelStatusValidator::class),

    TucModalityCreateUseCase::class => autowire(TucModalityCreateUseCase::class),
    TucModalityDeleteUseCase::class => autowire(TucModalityDeleteUseCase::class),
    TucModalityUpdateUseCase::class => autowire(TucModalityUpdateUseCase::class),
    TucModalityListUseCase::class => autowire(TucModalityListUseCase::class),

    TucModalityCreateUseCaseHandler::class => autowire(TucModalityCreateUseCaseHandler::class),
    TucModalityDeleteUseCaseHandler::class => autowire(TucModalityDeleteUseCaseHandler::class),
    TucModalityUpdateUseCaseHandler::class => autowire(TucModalityUpdateUseCaseHandler::class),
    TucModalityListUseCaseHandler::class => autowire(TucModalityListUseCaseHandler::class),

    TucModalityValidator::class => autowire(TucModalityValidator::class),

    TucStatusCreateUseCase::class => autowire(TucStatusCreateUseCase::class),
    TucStatusDeleteUseCase::class => autowire(TucStatusDeleteUseCase::class),
    TucStatusUpdateUseCase::class => autowire(TucStatusUpdateUseCase::class),
    TucStatusListUseCase::class => autowire(TucStatusListUseCase::class),

    TucStatusCreateUseCaseHandler::class => autowire(TucStatusCreateUseCaseHandler::class),
    TucStatusDeleteUseCaseHandler::class => autowire(TucStatusDeleteUseCaseHandler::class),
    TucStatusUpdateUseCaseHandler::class => autowire(TucStatusUpdateUseCaseHandler::class),
    TucStatusListUseCaseHandler::class => autowire(TucStatusListUseCaseHandler::class),

    ProcedureTypeCreateUseCase::class => autowire(ProcedureTypeCreateUseCase::class),
    ProcedureTypeDeleteUseCase::class => autowire(ProcedureTypeDeleteUseCase::class),
    ProcedureTypeUpdateUseCase::class => autowire(ProcedureTypeUpdateUseCase::class),
    ProcedureTypeListUseCase::class => autowire(ProcedureTypeListUseCase::class),

    ProcedureTypeCreateUseCaseHandler::class => autowire(ProcedureTypeCreateUseCaseHandler::class),
    ProcedureTypeDeleteUseCaseHandler::class => autowire(ProcedureTypeDeleteUseCaseHandler::class),
    ProcedureTypeUpdateUseCaseHandler::class => autowire(ProcedureTypeUpdateUseCaseHandler::class),
    ProcedureTypeListUseCaseHandler::class => autowire(ProcedureTypeListUseCaseHandler::class),

    ProcedureTypeValidator::class => autowire(ProcedureTypeValidator::class),

    UserCodeTypeCreateUseCase::class => autowire(UserCodeTypeCreateUseCaseHandler::class),
    UserCodeTypeDeleteUseCase::class => autowire(UserCodeTypeDeleteUseCaseHandler::class),
    UserCodeTypeUpdateUseCase::class => autowire(UserCodeTypeUpdateUseCaseHandler::class),
    UserCodeTypeListUseCase::class => autowire(UserCodeTypeListUseCaseHandler::class),

    UserCodeTypeCreateUseCaseHandler::class => autowire(UserCodeTypeCreateUseCaseHandler::class),
    UserCodeTypeDeleteUseCaseHandler::class => autowire(UserCodeTypeDeleteUseCaseHandler::class),
    UserCodeTypeUpdateUseCaseHandler::class => autowire(UserCodeTypeUpdateUseCaseHandler::class),
    UserCodeTypeListUseCaseHandler::class => autowire(UserCodeTypeListUseCaseHandler::class),


    TravelReportUseCase::class => autowire(TravelReportUseCaseHandler::class),
    TravelReportValidator::class => autowire(TravelReportValidator::class),

    UserReportUseCase::class => autowire(),
    UserReportValidator::class => autowire(UserReportValidator::class),

    VehicleReportUseCase::class => autowire(),
    VehicleReportValidator::class => autowire(VehicleReportValidator::class),

    IncidentReportUseCase::class => autowire(),
    IncidentReportValidator::class => autowire(IncidentReportValidator::class),

    InfractionReportUseCase::class => autowire(),
    InfractionReportValidator::class => autowire(InfractionReportValidator::class),

    AuditLogUseCase::class => autowire(),
    AuditLogValidator::class => autowire(AuditLogValidator::class),

    CreateAdminUserValidator::class => autowire(CreateAdminUserValidator::class),
);
