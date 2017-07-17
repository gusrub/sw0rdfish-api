<?php 

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;
use Sw0rdfish\Models\Secret as Secret;
use Sw0rdfish\Models\CreditCardSecret as CreditCardSecret;

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
    $user = new User();
    $user->valid();
    return $response->withJson($user->getValidationErrors());
  }
}
