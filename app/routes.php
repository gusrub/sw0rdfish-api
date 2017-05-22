<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response) {
    return $response->withJson(['foo'=>'bar']);
});

$app->get('/users', Sw0rdfish\Controllers\UserController::class . ':index');