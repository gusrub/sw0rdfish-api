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

    function index($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');
            $tokens = UserToken::all([
                'where' => [
                    'userId' => $user->id
                ]
            ]);
            return $response->withJson($tokens);
        } catch (ValidationException $e) {
            return $response->withJson($e->errors);
        }
    }

    function create($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');
            $params = $request->getParsedBody();
            $token = new UserToken($params);
            $token->userId = $user->id;
            $token->save();

            return $response->withJson($token)
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
            $token = $request->getAttribute('userToken');

            return $response->withJson($token)
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
            $token = $request->getAttribute('userToken');
            $token->save($params);

            return $response->withJson($token)
                            ->withStatus(200);
        } catch (Exception $e) {
            return $response->withJson($e->message)
                            ->withStatus(400);
        }
    }

    function destroy($request, $response, $args)
    {
        try {
            UserToken::delete($args['tokenId']);
            return $response->withStatus(203);
        } catch (Exception $e) {
            return $response->withJson($e->message)
                          ->withStatus(400);
        }
    }

}
