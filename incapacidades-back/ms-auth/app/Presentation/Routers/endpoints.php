<?php

use App\Controllers\AuthController;
use Slim\App;

return function (App $app) {
    $controller = new AuthController();

    $app->post('/api/auth/login', [$controller, 'login']);
    $app->post('/api/auth/logout', [$controller, 'logout']);
    $app->get('/api/auth/validar', [$controller, 'validar']);
};