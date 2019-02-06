<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Sw0rdfish\Application as Application;
use \Sw0rdfish\Helpers\I18n;
use \Sw0rdfish\Middleware\ResourceLoader;

$this->get('/', function (Request $request, Response $response) {
    return $response->withJson([
        'message' => I18n::translate(
            "This is an API application. Nothing to see here!"
        ),
        'level' => 'info',
        'data' => null
    ]);
});

// USERS ROUTES
$app = $this;
$this->group('/users', function ($app) {
    $this->map(['GET'], '', Sw0rdfish\Controllers\UsersController::class . ':index');
    $this->map(['POST'], '', Sw0rdfish\Controllers\UsersController::class . ':create');
    $this->group('/{userId:[0-9]+}', function($app) {
        $this->map(['GET'], '', Sw0rdfish\Controllers\UsersController::class . ':show');
        $this->map(['PUT', 'PATCH'], '', Sw0rdfish\Controllers\UsersController::class . ':update');
        $this->map(['DELETE'], '', Sw0rdfish\Controllers\UsersController::class . ':destroy');
        // TOKENS ROUTES
        $this->group('/tokens', function($app) {
            $this->map(['GET'], '', Sw0rdfish\Controllers\UserTokensController::class . ':index');
            $this->map(['POST'], '', Sw0rdfish\Controllers\UserTokensController::class . ':create');
            $this->group('/{tokenId:[0-9]+}', function($app) {
                $this->map(['GET'], '', Sw0rdfish\Controllers\UserTokensController::class . ':show');
                $this->map(['PUT', 'PATCH'], '', Sw0rdfish\Controllers\UserTokensController::class . ':update');
                $this->map(['DELETE'], '', Sw0rdfish\Controllers\UserTokensController::class . ':destroy');
            })->add(function($request, $response, $next){
                $request = \Sw0rdfish\Middleware\ResourceLoader::load(
                    'UserToken',
                    'tokenId',
                    $request,
                    $response
                );

                $response = $next($request, $response);
                return $response;
            });
        });
        // SECRETS ROUTES
        $this->group('/secrets', function($app){
            $this->map(['GET'], '', Sw0rdfish\Controllers\SecretsController::class . ':index');
            $this->map(['POST'], '', Sw0rdfish\Controllers\SecretsController::class . ':create');
            $this->group('/{secretId:[0-9]+}', function($app){
                $this->map(['GET'], '', Sw0rdfish\Controllers\SecretsController::class . ':show');
                $this->map(['PUT', 'PATCH'], '', Sw0rdfish\Controllers\SecretsController::class . ':update');
                $this->map(['DELETE'], '', Sw0rdfish\Controllers\SecretsController::class . ':destroy');
            })->add(function($request, $response, $next){
                $request = \Sw0rdfish\Middleware\ResourceLoader::load(
                    'Secret',
                    'secretId',
                    $request,
                    $response
                );

                $response = $next($request, $response);
                return $response;
            });
        });
    })->add(function($request, $response, $next) {
        $request = \Sw0rdfish\Middleware\ResourceLoader::load(
            'User',
            'userId',
            $request,
            $response
        );

        // set the service to load resources
        // $this['resourceLoaderService'] = function ($container) {
        //     $resourceLoaderService = new \Sw0rdfish\Services\ResourceLoaderService('User');
        //     return $resourceLoaderService;
        // };

        $response = $next($request, $response);
        return $response;
    });
});
