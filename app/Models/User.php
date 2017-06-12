<?php

namespace Sw0rdfish\Models;

/**
*
*/
class User extends BaseModel
{

    const ROLES = ["admin", "user", "super"];
    const TABLE_NAME = 'users';
    const VALIDATIONS = [
        "firstName" => [
            "presence"
        ],
        "lastName" => [
            "presence"
        ],
        "email" => [
            "presence",
            "validEmail",
            "uniqueness"
        ],
        "role" => [
            "presence",
            "inclusion" => [
                self::ROLES
            ]
        ]
    ];

    public $firstName;
    public $lastName;
    public $email;
    public $password;
    public $role;

}