<?php

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
use Sw0rdfish\Models\UserToken as UserToken;
use Sw0rdfish\Models\Secret as Secret;
use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\ValidationException as ValidationException;

/**
 * Manages all requests for users management.
 */
class UsersController extends BaseController
{

    /**
     * Request to get a list of available users.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     *  request object.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return Array A list of available users.
     */
    function index($request, $response, $args)
    {
        try {
            $users = User::all();
            return $response->withJson($users);
        } catch (ValidationException $e) {
            return $response->withJson($e->errors);
        }
    }

    /**
     * Request to create a new user.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     *  request object.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return String The JSON of the newly created user.
     */
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

    /**
     * Request to get the information of a certain user.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     *  request object.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return String The JSON of the requested user.
     */
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

    /**
     * Request to update the information of a certain user.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     *  request object.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return String The JSON of the updated user.
     */
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

    /**
     * Request to delete a user from the system.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     *  request object.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return null This endpoint won't return any value.
     */
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
