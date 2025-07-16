<?php

use FastRoute\RouteCollector;
use itaxcix\Infrastructure\Auth\Middleware\JwtMiddleware;
use itaxcix\Infrastructure\Auth\Middleware\JwtPermissionMiddleware;
use itaxcix\Infrastructure\Web\Controller\api\Admission\DriverApprovalController;
use itaxcix\Infrastructure\Web\Controller\api\Admission\PendingDriversController;
use itaxcix\Infrastructure\Web\Controller\api\Auth\AuthController;
use itaxcix\Infrastructure\Web\Controller\api\Auth\BiometricValidationController;
use itaxcix\Infrastructure\Web\Controller\api\Auth\DocumentValidationController;
use itaxcix\Infrastructure\Web\Controller\api\Auth\RecoveryController;
use itaxcix\Infrastructure\Web\Controller\api\Auth\RegistrationController;
use itaxcix\Infrastructure\Web\Controller\api\Auth\VehicleValidationController;
use itaxcix\Infrastructure\Web\Controller\api\District\DistrictController;
use itaxcix\Infrastructure\Web\Controller\api\Driver\DriverTucStatusController;
use itaxcix\Infrastructure\Web\Controller\api\Emergency\EmergencyNumberController;
use itaxcix\Infrastructure\Web\Controller\api\Incident\RegisterIncidentController;
use itaxcix\Infrastructure\Web\Controller\api\Incident\GetUserIncidentsController;
use itaxcix\Infrastructure\Web\Controller\api\Profile\ProfileContactController;
use itaxcix\Infrastructure\Web\Controller\api\Profile\ProfileController;
use itaxcix\Infrastructure\Web\Controller\api\Rating\RatingController;
use itaxcix\Infrastructure\Web\Controller\api\Travel\TravelController;
use itaxcix\Infrastructure\Web\Controller\api\Travel\TravelStatusController;
use itaxcix\Infrastructure\Web\Controller\api\User\UserProfilePhotoController;
use itaxcix\Infrastructure\Web\Controller\api\User\UserProfilePhotoUploadController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\CategoryController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\VehicleAssociationController;
use itaxcix\Infrastructure\Web\Controller\docs\DocsController;
use itaxcix\Infrastructure\Web\Controller\api\HelpCenter\HelpCenterController;
use itaxcix\Infrastructure\Web\Controller\api\Company\CompanyController;
use itaxcix\Infrastructure\Web\Controller\api\User\UserRoleTransitionController;

// Controladores de administración existentes
use itaxcix\Infrastructure\Web\Controller\web\Admin\AdminRoleController;
use itaxcix\Infrastructure\Web\Controller\web\Admin\AdminUserController;
use itaxcix\Infrastructure\Web\Controller\web\Admin\AdminPermissionController;
use itaxcix\Infrastructure\Web\Controller\web\Admin\AdminDashboardController;

// Controladores para Tablas Maestras
use itaxcix\Infrastructure\Web\Controller\api\Configuration\ConfigurationController;
use itaxcix\Infrastructure\Web\Controller\api\User\UserStatusController;
use itaxcix\Infrastructure\Web\Controller\api\User\ContactTypeController;
use itaxcix\Infrastructure\Web\Controller\api\Driver\DriverStatusController;
use itaxcix\Infrastructure\Web\Controller\api\person\DocumentTypeController;
use itaxcix\Infrastructure\Web\Controller\api\location\DepartmentController;
use itaxcix\Infrastructure\Web\Controller\api\location\ProvinceController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\BrandController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\FuelTypeController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\VehicleClassController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\ModelController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\ServiceTypeController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\ColorController;
use itaxcix\Infrastructure\Web\Controller\api\Incident\IncidentTypeController;
use itaxcix\Infrastructure\Web\Controller\api\Infraction\InfractionSeverityController;
use itaxcix\Infrastructure\Web\Controller\api\Infraction\InfractionStatusController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\TucModalityController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\TucStatusController;
use itaxcix\Infrastructure\Web\Controller\api\Vehicle\ProcedureTypeController;
use itaxcix\Infrastructure\Web\Controller\api\User\UserCodeTypeController;

// Controladores para Auditoría y Reportes
use itaxcix\Infrastructure\Web\Controller\api\Audit\AuditController;
use itaxcix\Infrastructure\Web\Controller\api\Reports\TravelReportController;
use itaxcix\Infrastructure\Web\Controller\api\Reports\UserReportController;
use itaxcix\Infrastructure\Web\Controller\api\Reports\VehicleReportController;
use itaxcix\Infrastructure\Web\Controller\api\Reports\IncidentReportController;
use itaxcix\Infrastructure\Web\Controller\api\Reports\InfractionReportController;
use itaxcix\Infrastructure\Web\Controller\api\Reports\RatingReportController;

return function (RouteCollector $r) {
    // API Routes v1
    $r->addGroup('/api/v1', callback: function ($r) {
        // Auth Routes
        $r->post('/auth/login', [AuthController::class, 'login']);

        //Validation Routes
        $r->post('/auth/validation/vehicle', [VehicleValidationController::class, 'validateVehicleWithDocument']);
        $r->post('/auth/validation/document', [DocumentValidationController::class, 'validateDocument']);
        $r->post('/auth/validation/biometric', [BiometricValidationController::class, 'validateBiometric']);

        // Registration Routes
        $r->post('/auth/registration', [RegistrationController::class, 'submitRegistrationData']);
        $r->post('/auth/registration/resend-code', [RegistrationController::class, 'resendContactCode']);
        $r->post('/auth/registration/verify-code', [RegistrationController::class, 'verifyContactCode']);

        // Recovery Routes
        $r->post('/auth/recovery/start', [RecoveryController::class, 'startPasswordRecovery']);
        $r->post('/auth/recovery/verify-code', [RecoveryController::class, 'verifyRecoveryCode']);
        $r->post('/auth/recovery/change-password', [JwtMiddleware::class, [RecoveryController::class, 'changePassword']]);

        // Admission Routes
        $r->get('/drivers/pending', [JwtPermissionMiddleware::class, 'ADMISIÓN DE CONDUCTORES', [PendingDriversController::class, 'getAllPendingDrivers']]);
        $r->get('/drivers/pending/{id}', [JwtPermissionMiddleware::class, 'ADMISIÓN DE CONDUCTORES', [PendingDriversController::class, 'getDriverDetails']]);
        $r->post('/drivers/approve', [JwtPermissionMiddleware::class, 'ADMISIÓN DE CONDUCTORES', [DriverApprovalController::class, 'approveDriver']]);
        $r->post('/drivers/reject', [JwtPermissionMiddleware::class, 'ADMISIÓN DE CONDUCTORES', [DriverApprovalController::class, 'rejectDriver']]);

        // Driver Routes
        $r->get('/drivers/{id}/has-active-tuc', [JwtPermissionMiddleware::class, 'INICIO CONDUCTOR', [DriverTucStatusController::class, 'hasActiveTuc']]);
        $r->get('/drivers/{driverId}/update-tuc', [JwtPermissionMiddleware::class, 'PERFIL CONDUCTOR', [DriverTucStatusController::class, 'updateDriverTuc']]);

        // User Routes
        $r->get('/users/{id}/profile-photo', [JwtMiddleware::class, [UserProfilePhotoController::class, 'getProfilePhoto']]);
        $r->post('/users/{id}/profile-photo', [JwtMiddleware::class, [UserProfilePhotoUploadController::class, 'uploadProfilePhoto']]);
        $r->get('/users/{userId}/travels/history', [JwtMiddleware::class, [TravelController::class, 'getTravelHistory']]);
        $r->get('/users/{userId}/ratings/comments', [JwtMiddleware::class, [RatingController::class, 'getUserRatingsComments']]);
        $r->delete('/users/{userId}/vehicle/association', [JwtPermissionMiddleware::class, 'PERFIL CONDUCTOR', [VehicleAssociationController::class, 'disassociateVehicle']]);
        $r->post('/users/{userId}/vehicle/association', [JwtPermissionMiddleware::class, 'PERFIL CONDUCTOR', [VehicleAssociationController::class, 'associateVehicle']]);
        $r->post('/users/request-citizen-role', [JwtPermissionMiddleware::class, 'PERFIL CONDUCTOR', [UserRoleTransitionController::class, 'transitionToCitizen']]);
        $r->post('/users/request-driver-role', [JwtPermissionMiddleware::class, 'PERFIL CIUDADANO', [UserRoleTransitionController::class, 'transitionToDriver']]);

        // Profile Routes
        $r->get('/profile/admin/{userId}', [JwtMiddleware::class, [ProfileController::class, 'getAdminProfile']]);
        $r->get('/profile/citizen/{userId}', [JwtPermissionMiddleware::class, 'PERFIL CIUDADANO', [ProfileController::class, 'getCitizenProfile']]);
        $r->get('/profile/driver/{userId}', [JwtPermissionMiddleware::class, 'PERFIL CONDUCTOR', [ProfileController::class, 'getDriverProfile']]);

        // Profile Contact Routes
        $r->post('/profile/change-email', [JwtMiddleware::class, [ProfileContactController::class, 'changeEmail']]);
        $r->post('/profile/verify-email', [JwtMiddleware::class, [ProfileContactController::class, 'verifyEmail']]);
        $r->post('/profile/change-phone', [JwtMiddleware::class, [ProfileContactController::class, 'changePhone']]);
        $r->post('/profile/verify-phone', [JwtMiddleware::class, [ProfileContactController::class, 'verifyPhone']]);

        // Travel Routes
        $r->post('/travels', [JwtPermissionMiddleware::class, 'INICIO CIUDADANO', [TravelStatusController::class, 'requestTravel']]);
        $r->patch('/travels/{travelId}/respond', [JwtPermissionMiddleware::class, 'INICIO CONDUCTOR', [TravelStatusController::class, 'respondToRequest']]);
        $r->patch('/travels/{travelId}/start', [JwtPermissionMiddleware::class, 'INICIO CONDUCTOR', [TravelStatusController::class, 'startTravel']]);
        $r->patch('/travels/{travelId}/complete', [JwtPermissionMiddleware::class, 'INICIO CONDUCTOR', [TravelStatusController::class, 'completeTravel']]);
        $r->patch('/travels/{travelId}/cancel', [JwtMiddleware::class, [TravelStatusController::class, 'cancelTravel']]);
        $r->post('/travels/{travelId}/rate', [JwtMiddleware::class, [TravelController::class, 'rateTravel']]);
        $r->get('/travels/{travelId}/ratings', [JwtMiddleware::class, [TravelController::class, 'getTravelRatingsByTravel']]);

        // Emergency Routes
        $r->get('/emergency/number', [JwtMiddleware::class, [EmergencyNumberController::class, 'getEmergencyNumber']]);
        $r->post('/emergency/number', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [EmergencyNumberController::class, 'saveEmergencyNumber']]);

        // Incident Routes
        $r->post('/incidents/register', [JwtMiddleware::class, [RegisterIncidentController::class, 'register']]);
        $r->get('/incidents/user/{userId}', [JwtMiddleware::class, [GetUserIncidentsController::class, 'getUserIncidents']]);

        // ===============================
        // TABLAS MAESTRAS - RUTAS COMPLETAS
        // ===============================

        // Company Routes (ya existente - se mantiene)
        $r->get('/admin/companies', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [CompanyController::class, 'list']]);
        $r->post('/admin/companies', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [CompanyController::class, 'create']]);
        $r->put('/admin/companies/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [CompanyController::class, 'update']]);
        $r->delete('/admin/companies/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [CompanyController::class, 'delete']]);

        // Help Center Routes (ya existente - se mantiene)
        $r->get('/help-center', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [HelpCenterController::class, 'list']]);
        $r->get('/help-center/public', [HelpCenterController::class, 'publicList']);
        $r->post('/help-center', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [HelpCenterController::class, 'create']]);
        $r->put('/help-center/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [HelpCenterController::class, 'update']]);
        $r->delete('/help-center/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [HelpCenterController::class, 'delete']]);

        // Configuration Routes
        $r->get('/admin/configurations', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [ConfigurationController::class, 'list']]);
        $r->post('/admin/configurations', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [ConfigurationController::class, 'create']]);
        $r->put('/admin/configurations/{id}', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [ConfigurationController::class, 'update']]);
        $r->delete('/admin/configurations/{id}', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [ConfigurationController::class, 'delete']]);
        $r->get('/admin/configurations/predefined-keys', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [ConfigurationController::class, 'getPredefinedKeys']]);

        // User Status Routes
        $r->get('/admin/user-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [UserStatusController::class, 'list']]);
        $r->post('/admin/user-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [UserStatusController::class, 'create']]);
        $r->put('/admin/user-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [UserStatusController::class, 'update']]);
        $r->delete('/admin/user-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [UserStatusController::class, 'delete']]);

        // Contact Type Routes
        $r->get('/admin/contact-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ContactTypeController::class, 'list']]);
        $r->post('/admin/contact-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ContactTypeController::class, 'create']]);
        $r->put('/admin/contact-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ContactTypeController::class, 'update']]);
        $r->delete('/admin/contact-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ContactTypeController::class, 'delete']]);

        // Driver Status Routes
        $r->get('/admin/driver-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DriverStatusController::class, 'list']]);
        $r->post('/admin/driver-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DriverStatusController::class, 'create']]);
        $r->put('/admin/driver-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DriverStatusController::class, 'update']]);
        $r->delete('/admin/driver-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DriverStatusController::class, 'delete']]);

        // Document Type Routes
        $r->get('/admin/document-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DocumentTypeController::class, 'list']]);
        $r->post('/admin/document-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DocumentTypeController::class, 'create']]);
        $r->put('/admin/document-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DocumentTypeController::class, 'update']]);
        $r->delete('/admin/document-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DocumentTypeController::class, 'delete']]);

        // Location Routes
        $r->get('/admin/departments', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DepartmentController::class, 'list']]);
        $r->post('/admin/departments', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DepartmentController::class, 'create']]);
        $r->put('/admin/departments/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DepartmentController::class, 'update']]);
        $r->delete('/admin/departments/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DepartmentController::class, 'delete']]);

        $r->get('/admin/provinces', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ProvinceController::class, 'list']]);
        $r->post('/admin/provinces', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ProvinceController::class, 'create']]);
        $r->put('/admin/provinces/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ProvinceController::class, 'update']]);
        $r->delete('/admin/provinces/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ProvinceController::class, 'delete']]);

        // District Routes
        $r->get('/admin/districts', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DistrictController::class, 'list']]);
        $r->post('/admin/districts', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DistrictController::class, 'create']]);
        $r->put('/admin/districts/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DistrictController::class, 'update']]);
        $r->delete('/admin/districts/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [DistrictController::class, 'delete']]);

        // Vehicle Master Data Routes
        $r->get('/admin/brands', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [BrandController::class, 'list']]);
        $r->post('/admin/brands', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [BrandController::class, 'create']]);
        $r->put('/admin/brands/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [BrandController::class, 'update']]);
        $r->delete('/admin/brands/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [BrandController::class, 'delete']]);

        $r->get('/admin/models', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ModelController::class, 'list']]);
        $r->post('/admin/models', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ModelController::class, 'create']]);
        $r->put('/admin/models/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ModelController::class, 'update']]);
        $r->delete('/admin/models/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ModelController::class, 'delete']]);

        $r->get('/admin/colors', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ColorController::class, 'list']]);
        $r->post('/admin/colors', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ColorController::class, 'create']]);
        $r->put('/admin/colors/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ColorController::class, 'update']]);
        $r->delete('/admin/colors/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ColorController::class, 'delete']]);

        $r->get('/admin/fuel-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [FuelTypeController::class, 'list']]);
        $r->post('/admin/fuel-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [FuelTypeController::class, 'create']]);
        $r->put('/admin/fuel-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [FuelTypeController::class, 'update']]);
        $r->delete('/admin/fuel-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [FuelTypeController::class, 'delete']]);

        $r->get('/admin/categories', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [CategoryController::class, 'list']]);
        $r->post('/admin/categories', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [CategoryController::class, 'create']]);
        $r->put('/admin/categories/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [CategoryController::class, 'update']]);
        $r->delete('/admin/categories/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [CategoryController::class, 'delete']]);

        $r->get('/admin/vehicle-classes', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [VehicleClassController::class, 'list']]);
        $r->post('/admin/vehicle-classes', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [VehicleClassController::class, 'create']]);
        $r->put('/admin/vehicle-classes/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [VehicleClassController::class, 'update']]);
        $r->delete('/admin/vehicle-classes/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [VehicleClassController::class, 'delete']]);

        // Service & Incident Master Data Routes
        $r->get('/admin/service-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ServiceTypeController::class, 'list']]);
        $r->post('/admin/service-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ServiceTypeController::class, 'create']]);
        $r->put('/admin/service-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ServiceTypeController::class, 'update']]);
        $r->delete('/admin/service-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ServiceTypeController::class, 'delete']]);

        $r->get('/admin/incident-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [IncidentTypeController::class, 'list']]);
        $r->post('/admin/incident-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [IncidentTypeController::class, 'create']]);
        $r->put('/admin/incident-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [IncidentTypeController::class, 'update']]);
        $r->delete('/admin/incident-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [IncidentTypeController::class, 'delete']]);

        $r->get('/admin/infraction-severities', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [InfractionSeverityController::class, 'list']]);
        $r->post('/admin/infraction-severities', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [InfractionSeverityController::class, 'create']]);
        $r->put('/admin/infraction-severities/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [InfractionSeverityController::class, 'update']]);
        $r->delete('/admin/infraction-severities/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [InfractionSeverityController::class, 'delete']]);

        $r->get('/admin/infraction-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [InfractionStatusController::class, 'list']]);
        $r->post('/admin/infraction-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [InfractionStatusController::class, 'create']]);
        $r->put('/admin/infraction-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [InfractionStatusController::class, 'update']]);
        $r->delete('/admin/infraction-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [InfractionStatusController::class, 'delete']]);

        $r->get('/admin/travel-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TravelStatusController::class, 'list']]);
        $r->post('/admin/travel-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TravelStatusController::class, 'create']]);
        $r->put('/admin/travel-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TravelStatusController::class, 'update']]);
        $r->delete('/admin/travel-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TravelStatusController::class, 'delete']]);

        // TUC Master Data Routes
        $r->get('/admin/tuc-modalities', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TucModalityController::class, 'list']]);
        $r->post('/admin/tuc-modalities', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TucModalityController::class, 'create']]);
        $r->put('/admin/tuc-modalities/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TucModalityController::class, 'update']]);
        $r->delete('/admin/tuc-modalities/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TucModalityController::class, 'delete']]);

        $r->get('/admin/tuc-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TucStatusController::class, 'list']]);
        $r->post('/admin/tuc-statuses', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TucStatusController::class, 'create']]);
        $r->put('/admin/tuc-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TucStatusController::class, 'update']]);
        $r->delete('/admin/tuc-statuses/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [TucStatusController::class, 'delete']]);

        $r->get('/admin/procedure-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ProcedureTypeController::class, 'list']]);
        $r->post('/admin/procedure-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ProcedureTypeController::class, 'create']]);
        $r->put('/admin/procedure-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ProcedureTypeController::class, 'update']]);
        $r->delete('/admin/procedure-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [ProcedureTypeController::class, 'delete']]);

        // User Code Type Routes
        $r->get('/admin/user-code-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [UserCodeTypeController::class, 'list']]);
        $r->post('/admin/user-code-types', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [UserCodeTypeController::class, 'create']]);
        $r->put('/admin/user-code-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [UserCodeTypeController::class, 'update']]);
        $r->delete('/admin/user-code-types/{id}', [JwtPermissionMiddleware::class, 'TABLAS MAESTRAS', [UserCodeTypeController::class, 'delete']]);

        // ===============================
        // AUDITORÍA - RUTAS COMPLETAS
        // ===============================

        // Audit Routes - Solo lectura con filtros avanzados
        $r->get('/audit', [JwtPermissionMiddleware::class, 'AUDITORIA', [AuditController::class, 'list']]);
        $r->get('/audit/export', [JwtPermissionMiddleware::class, 'AUDITORIA', [AuditController::class, 'exportAuditLog']]);
        $r->get('/audit/{id}', [JwtPermissionMiddleware::class, 'AUDITORIA', [AuditController::class, 'getDetails']]);
        $r->get('/audit/user/{userId}', [JwtPermissionMiddleware::class, 'AUDITORIA', [AuditController::class, 'getUserAuditLog']]);
        $r->get('/audit/entity/{entityType}', [JwtPermissionMiddleware::class, 'AUDITORIA', [AuditController::class, 'getEntityAuditLog']]);
        $r->get('/audit/action/{action}', [JwtPermissionMiddleware::class, 'AUDITORIA', [AuditController::class, 'getActionAuditLog']]);

        // Transactional Data Reports - Para análisis de tablas transaccionales
        $r->get('/reports/travels', [JwtPermissionMiddleware::class, 'AUDITORIA', [TravelReportController::class, 'getReport']]);
        $r->get('/reports/users', [JwtPermissionMiddleware::class, 'AUDITORIA', [UserReportController::class, 'getReport']]);
        $r->get('/reports/vehicles', [JwtPermissionMiddleware::class, 'AUDITORIA', [VehicleReportController::class, 'getReport']]);
        $r->get('/reports/incidents', [JwtPermissionMiddleware::class, 'AUDITORIA', [IncidentReportController::class, 'getReport']]);
        $r->get('/reports/infractions', [JwtPermissionMiddleware::class, 'AUDITORIA', [InfractionReportController::class, 'getReport']]);
        $r->get('/reports/ratings', [JwtPermissionMiddleware::class, 'AUDITORIA', [RatingReportController::class, 'getReport']]);

        // Dashboard Routes (ya existente - se mantiene)
        $r->get('/dashboard/stats', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminDashboardController::class, 'getStats']]);

        // Role Management Routes (CRUD completo + gestión masiva)
        $r->get('/roles', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminRoleController::class, 'listRoles']]);
        $r->post('/roles', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminRoleController::class, 'createRole']]);
        $r->put('/roles/{roleId}', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminRoleController::class, 'updateRole']]);
        $r->delete('/roles/{roleId}', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminRoleController::class, 'deleteRole']]);
        $r->get('/roles/{roleId}/permissions', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminRoleController::class, 'getRoleWithPermissions']]);
        $r->post('/roles/{roleId}/permissions', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminRoleController::class, 'assignPermissionsToRole']]);

        // User Management Routes (CRUD completo + gestión avanzada)
        $r->get('/users', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminUserController::class, 'adminListUsers']]); // Usar la versión avanzada
        $r->post('/users', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminUserController::class, 'createAdminUser']]); // Crear usuario administrador
        $r->get('/users/{userId}', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminUserController::class, 'getUserDetails']]); // Detalles completos del usuario
        $r->get('/users/{userId}/roles', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminUserController::class, 'getUserWithRoles']]); // Usuario con roles específico
        $r->put('/users/{userId}/roles', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminUserController::class, 'updateUserRoles']]); // Usar la versión admin
        $r->put('/users/{userId}/status', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminUserController::class, 'changeUserStatus']]);
        $r->post('/users/{userId}/verify-contact', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminUserController::class, 'forceVerifyContact']]);
        $r->post('/users/{userId}/reset-password', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminUserController::class, 'resetUserPassword']]);

        // Permission Management Routes
        $r->get('/permissions', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminPermissionController::class, 'listPermissions']]);
        $r->post('/permissions', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminPermissionController::class, 'createPermission']]);
        $r->put('/permissions/{id}', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminPermissionController::class, 'updatePermission']]);
        $r->delete('/permissions/{id}', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [AdminPermissionController::class, 'deletePermission']]);

    }); // ← Cierre del grupo /api/v1

    // Web Routes v1
    $r->addGroup('/web/v1', function ($r) {
        // Documentation routes
        $r->get('/docs', [DocsController::class, 'index']);
        $r->get('/websocket-docs', [DocsController::class, 'websocketDocs']);
    });
};
