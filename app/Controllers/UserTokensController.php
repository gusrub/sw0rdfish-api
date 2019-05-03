<?php

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
use Sw0rdfish\Models\UserToken as UserToken;
use Sw0rdfish\Errors\BadRequestError;
use Sw0rdfish\Errors\AuthenticationError;
use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\ValidationException as ValidationException;

/**
 * Manages all requests for security tokens.
 */
class UserTokensController extends BaseController
{

    /**
     * Request to create a new user token.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The incoming
     * request object. Request payload should have a valid JSON representation
     *  of the desired new user data.
     * @param \Psr\Http\Message\ResponseInterface $response The response that
     *  will be used to return any data.
     * @param Array $args An array containing any URL or Query-String generated
     *  arguments.
     * @return String The JSON of the newly created user token.
     */
    function create($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            $service = new \Sw0rdfish\Services\CreateTokenService($request);

            if ($service->perform()) {
                $token = $service->output;
            } else {
                throw new AuthenticationError(
                    'There was a problem creating the token',
                    $service->errors
                );
            }

            return $response->withJson($token)
                            ->withStatus(201);
        } catch (ValidationException $e) {
            return $response->withJson($e->errors)
                          ->withStatus(400);
        }
    }

}
