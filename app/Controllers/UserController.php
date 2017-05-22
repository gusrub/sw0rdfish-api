<?php 

namespace Sw0rdfish\Controllers;

/**
* 
*/
class UserController
{
  
  protected $container;

  function __construct($container)
  {
    $this->container = container;
  }

  function index($request, $response, $args) 
  {
    return $response->withJson(['We are in:'=>'Users controller']);
  }
}