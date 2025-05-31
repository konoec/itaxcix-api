<?php

use FastRoute\RouteCollector;
use itaxcix\Infrastructure\Auth\Middleware\JwtMiddleware;
use itaxcix\Infrastructure\Web\Controller\api\AuthController;
use itaxcix\Infrastructure\Web\Controller\api\BiometricValidationController;
use itaxcix\Infrastructure\Web\Controller\api\DocumentValidationController;
use itaxcix\Infrastructure\Web\Controller\api\RecoveryController;
use itaxcix\Infrastructure\Web\Controller\api\RegistrationController;
use itaxcix\Infrastructure\Web\Controller\api\VehicleValidationController;
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
        $r->post('/auth/registration/verify-code', [RegistrationController::class, 'verifyContactCode']);

        // Recovery Routes
        $r->post('/auth/recovery/start', [RecoveryController::class, 'startPasswordRecovery']);
        $r->post('/auth/recovery/verify-code', [RecoveryController::class, 'verifyRecoveryCode']);
        $r->post('/auth/recovery/change-password', [JwtMiddleware::class, [RecoveryController::class, 'changePassword']]);
    });

    // Web Routes v1
    $r -> addGroup('/web/v1', function ($r) {
        // Documentation routes
        $r->get('/docs', [DocsController::class, 'index']);
    });
};