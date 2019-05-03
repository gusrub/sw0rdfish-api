<?php

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
use Sw0rdfish\Models\Secret as Secret;
use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\ValidationException as ValidationException;

/**
 * Manages all requests for secrets management.
 */
class SecretsController extends BaseController
{

    /**
     * Request to get a list of a user's secrets.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     *  request object.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return Array A list of the secrets for the given user.
     */
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

    /**
     * Request to create a new user secret.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     * request object. Request payload should have a valid JSON representation
     *  of the desired new user data.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return String The JSON of the newly created user secret.
     */
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

    /**
     * Request to get the data for a user secret.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     *  request object.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return String The JSON of the requested user secret.
     */
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

    /**
     * Request to update the information of a certain user secret.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     *  request object. Request payload should have a valid JSON representation
     *  of the data to be updated for that user.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return String The JSON of the updated user secret.
     */
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

    /**
     * Request to delete a user secret from the system.
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
            Secret::delete($args['secretId']);
            return $response->withStatus(203);
        } catch (Exception $e) {
            return $response->withJson($e->message)
                          ->withStatus(400);
        }
    }
}
