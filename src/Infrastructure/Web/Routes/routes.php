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
use itaxcix\Infrastructure\Web\Controller\api\Driver\DriverTucStatusController;
use itaxcix\Infrastructure\Web\Controller\api\Emergency\EmergencyNumberController;
use itaxcix\Infrastructure\Web\Controller\api\Travel\TravelStatusController;
use itaxcix\Infrastructure\Web\Controller\api\User\UserProfilePhotoController;
use itaxcix\Infrastructure\Web\Controller\api\User\UserProfilePhotoUploadController;
use itaxcix\Infrastructure\Web\Controller\docs\DocsController;

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

        // User Routes
        $r->get('/users/{id}/profile-photo', [JwtMiddleware::class, [UserProfilePhotoController::class, 'getProfilePhoto']]);
        $r->post('/users/{id}/profile-photo', [JwtMiddleware::class, [UserProfilePhotoUploadController::class, 'uploadProfilePhoto']]);

        // Travel Routes
        $r->post('/travels', [JwtPermissionMiddleware::class, 'INICIO CIUDADANO', [TravelStatusController::class, 'requestTravel']]);
        $r->patch('/travels/{travelId}/respond', [JwtPermissionMiddleware::class, 'INICIO CONDUCTOR', [TravelStatusController::class, 'respondToRequest']]);
        $r->patch('/travels/{travelId}/start', [JwtPermissionMiddleware::class, 'INICIO CONDUCTOR', [TravelStatusController::class, 'startTravel']]);
        $r->patch('/travels/{travelId}/complete', [JwtPermissionMiddleware::class, 'INICIO CONDUCTOR', [TravelStatusController::class, 'completeTravel']]);
        $r->patch('/travels/{travelId}/cancel', [JwtMiddleware::class, [TravelStatusController::class, 'cancelTravel']]);

        // Emergency Routes
        $r->get('/emergency/number', [JwtMiddleware::class, [EmergencyNumberController::class, 'getEmergencyNumber']]);
        $r->post('/emergency/number', [JwtPermissionMiddleware::class, 'CONFIGURACIÓN', [EmergencyNumberController::class, 'saveEmergencyNumber']]);
    });

    // Web Routes v1
    $r->addGroup('/web/v1', function ($r) {
        // Documentation routes
        $r->get('/docs', [DocsController::class, 'index']);
        $r->get('/websocket-docs', [DocsController::class, 'websocketDocs']);
    });
};