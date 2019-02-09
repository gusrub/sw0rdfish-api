<?php

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
use Sw0rdfish\Models\UserToken as UserToken;
use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\ValidationException as ValidationException;

/**
*
*/
class UserTokensController
{

    protected $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    function create($request, $response, $args)
    {
        try {
            $params = $request->getParsedBody();
            $token = new UserToken();

            return $response->withJson($token)
                            ->withStatus(201);
        } catch (ValidationException $e) {
            return $response->withJson($e->errors)
                          ->withStatus(400);
        }
    }

}
