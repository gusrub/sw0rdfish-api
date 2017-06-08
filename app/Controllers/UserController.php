<?php 

namespace Sw0rdfish\Controllers;

use Sw0rdfish\Models\User as User;

/**
* 
*/
class UserController
{
  
  protected $container;

  function __construct($container)
  {
    $this->container = $container;
  }

  function index($request, $response, $args) 
  {
    $users = User::all([
      "orderBy" => "lastName",
      "sort" => "ASC",
      "page" => 1
    ]);
    return $response->withJson($users);
  }
}