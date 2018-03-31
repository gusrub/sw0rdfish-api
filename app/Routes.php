<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Sw0rdfish\Application as Application;
use \Sw0rdfish\Helpers\I18n;

$app->get('/', function (Request $request, Response $response) {
    return $response->withJson([
        'message' => I18n::translate(
            "This is an API application. Nothing to see here!"
        ),
        'level' => 'info',
        'data' => null
    ]);
});

$app->get('/users', Sw0rdfish\Controllers\UsersController::class . ':index');
