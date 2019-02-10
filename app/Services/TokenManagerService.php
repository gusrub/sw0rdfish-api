<?php

namespace Sw0rdfish\Services;

use \Sw0rdfish\Models\UserToken as UserToken;

/**
 * 
 */
class TokenManagerService
{
    public $request;
    public $token;

    function __construct($request)
    {
        $this->request = $request;
        $this->token = $this->createToken($request);
    }

    private function createToken($request)
    {
        $type = $this->validTokenType($request);

        switch ($type) {
            case 'session':
                return $this->createSessionToken($request);
            default:
                return null;
        }
    }

    private function validTokenType($request)
    {
        $params = $request->getParsedBody();

        if (in_array($params['type'], UserToken::TYPES) == false) {
            throw new Exception("You must specify a valid type of token", 1);
        }

        return $params['type'];
    }

    private function createSessionToken($request)
    {
        // 1 - find email and validate address
        // 2 - validate password
        // 3 - create token instance
        // 4 - set expiration of token
        // 5 - save token
        // 6 - return saved token
    }

}
