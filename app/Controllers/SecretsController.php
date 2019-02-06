<?php

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
use Sw0rdfish\Models\Secret as Secret;
use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\ValidationException as ValidationException;

/**
*
*/
class SecretsController
{
    protected $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    function index($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');
            $secrets = Secret::all([
                'where' => [
                    'userId' => $user->id
                ]
            ]);
            return $response->withJson($secrets);
        } catch (ValidationException $e) {
            return $response->withJson($e->errors);
        }
    }

    function create($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');
            $params = $request->getParsedBody();
            $secret = new Secret($params);
            $secret->userId = $user->id;
            $secret->save();

            return $response->withJson($secret)
                            ->withStatus(201);
        } catch (ValidationException $e) {
            return $response->withJson($e->errors)
                          ->withStatus(400);
        }
    }

    function show($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');
            $secret = $request->getAttribute('secret');

            return $response->withJson($secret)
                            ->withStatus(200);

        } catch (Exception $e) {
            return $response->withJson($e->message)
                            ->withStatus(400);
        }
    }

    function update($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');
            $params = $request->getParsedBody();
            $secret = $request->getAttribute('secret');

            if($secret->save($params)) {
                return $response->withJson($secret)
                                ->withStatus(200);
            } else {
                throw new ValidationException(
                    'Object has some invalid data',
                    $secret->getValidationErrors()
                );
            }
        } catch (\Exception $e) {
            return $response->withJson($e->message)
                            ->withStatus(400);
        }
    }

    function destroy($request, $response, $args)
    {
        try {
            Secret::delete($args['secretId']);
            return $response->withStatus(203);
        } catch (Exception $e) {
            return $response->withJson($e->message)
                          ->withStatus(400);
        }
    }
}
