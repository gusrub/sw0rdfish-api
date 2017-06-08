<?php

namespace Sw0rdfish\Models;

/**
* 
*/
class User extends BaseModel
{

    const TABLE_NAME = 'users';

    public $firstName;
    public $lastName;
    public $email;
    public $password;
    public $role;


}