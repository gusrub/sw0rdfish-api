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
        $this->token = new UserToken();
    }
}
