<?php

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
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
      $pages = User::pages();
      return $response->withJson(['pages'=>$pages]);
    } catch (ValidationException $e) {
      return $response->withJson($e->errors);
    }
  }
}
