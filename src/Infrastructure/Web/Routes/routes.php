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
use itaxcix\Infrastructure\Web\Controller\docs\DocsController;

return function (RouteCollector $r) {
    // API Routes v1
    $r->addGroup('/api/v1', function ($r) {
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
        $r->post('/drivers/pending/approve', [JwtPermissionMiddleware::class, 'ADMISIÓN DE CONDUCTORES', [DriverApprovalController::class, 'approveDriver']]);
        $r->post('/drivers/pending/reject', [JwtPermissionMiddleware::class, 'ADMISIÓN DE CONDUCTORES', [DriverApprovalController::class, 'rejectDriver']]);
    });

    // Web Routes v1
    $r -> addGroup('/web/v1', function ($r) {
        // Documentation routes
        $r->get('/docs', [DocsController::class, 'index']);
    });
};