<?php

use FastRoute\RouteCollector;
use itaxcix\controllers\AuthController;
use itaxcix\controllers\HelloController;

return function (RouteCollector $r) {
    $r->addGroup('/api/v1', function (RouteCollector $r) {

        $r->get('/hello/{name}', [HelloController::class, 'sayHello']);

        $r->post('/auth/login', [AuthController::class, 'login']);
        $r->post('/auth/register/citizen', [AuthController::class, 'registerCitizen']);
        $r->post('/auth/register/driver', [AuthController::class, 'registerDriver']);
        $r->post('/auth/recover/email', [AuthController::class, 'recoverByEmail']);
        $r->post('/auth/recover/phone', [AuthController::class, 'recoverByPhone']);
        $r->post('/auth/verify-code', [AuthController::class, 'verifyCode']);
        $r->post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    });
};