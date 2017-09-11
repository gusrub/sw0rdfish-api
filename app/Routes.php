<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Sw0rdfish\Application as Application;

$app->get('/', function (Request $request, Response $response) {
    return $response->withJson(['foo'=>'bar']);
});

$app->get('/users', Sw0rdfish\Controllers\UsersController::class . ':index');