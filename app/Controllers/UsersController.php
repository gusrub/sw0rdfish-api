<?php

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
use Sw0rdfish\Models\UserToken as UserToken;
use Sw0rdfish\Models\Secret as Secret;
use Sw0rdfish\Models\CreditCardSecret as CreditCardSecret;
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
    $this->container = $container;
  }

  function index($request, $response, $args)
  {
    try {
      $token = new UserToken();
      return $response->withJson($token);
    } catch (ValidationException $e) {
      return $response->withJson($e->errors);
    }
  }
}
