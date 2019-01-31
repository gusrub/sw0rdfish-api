<?php

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
use Sw0rdfish\Models\UserToken as UserToken;
use Sw0rdfish\Models\Secret as Secret;
use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\ValidationException as ValidationException;

/**
*
*/
class UsersController
{

    protected $container;

    function __construct($container)
    {
        // TODO: put this on a base controller
        $this->container = $container;
        // $resourceLoaderService = $container['resourceLoaderService'];
        // $this->resource = $resourceLoaderService->load();
    }

    function index($request, $response, $args)
    {
        try {
            $users = User::all();
            return $response->withJson($users);
        } catch (ValidationException $e) {
            return $response->withJson($e->errors);
        }
    }

    function create($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            $user = new User($params);
            $user->save();
            return $response->withJson($user)
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
            return $response->withJson($user)
                            ->withStatus(200);

        } catch (Exception $e) {
            return $response->withJson($e->message)
                            ->withStatus(400);
        }
    }

    function update($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            $user = $user = $request->getAttribute('user');
            $user->save($params);
            return $response->withJson($user)
                            ->withStatus(200);
        } catch (Exception $e) {
            return $response->withJson($e->message)
                            ->withStatus(400);
        }
    }

    function destroy($request, $response, $args)
    {
        try {
            User::delete($args['userId']);
            return $response->withStatus(203);
        } catch (Exception $e) {
            return $response->withJson($e->message)
                          ->withStatus(400);
        }
    }
}
