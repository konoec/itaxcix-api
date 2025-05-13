<?php

use FastRoute\RouteCollector;
use itaxcix\controllers\AuthController;
use itaxcix\controllers\DriverController;
use itaxcix\controllers\HelloController;
use itaxcix\middleware\JwtMiddleware;

return function (RouteCollector $r) {
    $r->addGroup('/api/v1', function (RouteCollector $r) {

        $r->get('/hello/{name}', [JwtMiddleware::class, HelloController::class . '@sayHello']);

        $r->post('/auth/login', [AuthController::class, 'login']);
        $r->post('/auth/register/citizen', [AuthController::class, 'registerCitizen']);
        $r->post('/auth/register/driver', [AuthController::class, 'registerDriver']);
        $r->post('/auth/recovery', [AuthController::class, 'requestRecovery']);
        $r->post('/auth/verify-code', [AuthController::class, 'verifyCode']);
        $r->post('/auth/reset-password', [AuthController::class, 'resetPassword']);

        $r->post('/driver/activate-availability', [JwtMiddleware::class, DriverController::class . '@activateAvailability']);
        $r->post('/driver/deactivate-availability', [JwtMiddleware::class, DriverController::class . '@deactivateAvailability']);
    });
};