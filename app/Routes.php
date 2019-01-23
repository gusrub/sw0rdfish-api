<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Sw0rdfish\Application as Application;
use \Sw0rdfish\Helpers\I18n;

$this->get('/', function (Request $request, Response $response) {
    return $response->withJson([
        'message' => I18n::translate(
            "This is an API application. Nothing to see here!"
        ),
        'level' => 'info',
        'data' => null
    ]);
});

$this->get('/users', Sw0rdfish\Controllers\UsersController::class . ':index');
$this->get('/users/{id}', Sw0rdfish\Controllers\UsersController::class . ':show');
$this->post('/users', Sw0rdfish\Controllers\UsersController::class . ':create');
$this->put('/users/{id}', Sw0rdfish\Controllers\UsersController::class . ':update');
$this->delete('/users/{id}', Sw0rdfish\Controllers\UsersController::class . ':destroy');
