<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Sw0rdfish\Application as Application;
use \Sw0rdfish\Helpers\I18n;
use \Sw0rdfish\Middleware\ResourceLoader;
use \Sw0rdfish\Errors\Handler;

$app = $this;

// Set custom error handler
$container = $app->getContainer();
$container['errorHandler'] = function ($container) {
    return new Handler();
};

$this->get('/', function (Request $request, Response $response) {
    return $response->withJson([
        'message' => I18n::translate(
            "This is an API application. Nothing to see here!"
        ),
        'level' => 'info',
        'data' => null
    ]);
});


// TOKENS ROUTES
$this->group('/tokens', function($app) {
    $this->map(
        ['POST'],
        '',
        Sw0rdfish\Controllers\UserTokensController::class . ':create'
    ); /*->add(function($request, $response, $next) {
        // load service to manage tokens
        $this['tokenManagerService'] = function ($container) use($request) {
            $tokenManagerService = new \Sw0rdfish\Services\TokenManagerService($request);
            return $tokenManagerService;
        };

        return $next($request, $response);
    });*/
});

// USERS ROUTES
$this->group('/users', function ($app) {
    $this->map(['GET'], '', Sw0rdfish\Controllers\UsersController::class . ':index');
    $this->map(['POST'], '', Sw0rdfish\Controllers\UsersController::class . ':create');
    $this->group('/{userId:[0-9]+}', function($app) {
        $this->map(['GET'], '', Sw0rdfish\Controllers\UsersController::class . ':show');
        $this->map(['PUT', 'PATCH'], '', Sw0rdfish\Controllers\UsersController::class . ':update');
        $this->map(['DELETE'], '', Sw0rdfish\Controllers\UsersController::class . ':destroy');

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

        $response = $next($request, $response);
        return $response;
    });
});
