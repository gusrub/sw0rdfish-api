<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to interact with the database.
 */
namespace Sw0rdfish\Models;

/**
* Represents a user on the system that can store secrets with a defined role.
*
* A user can have any of the following roles: `user`, `admin` or `super`. The difference is that a `user` role type can only manage his own secrets, an `admin` role type can invite other email addresses or people to use the system and can manage users but only of type `user` and finally a `super` role type may manage both users and admins.
*/
class User extends BaseModel
{

    /**
     * Defines the types of roles that can be assigned to a user.
     */
    const ROLES = ['admin', 'user', 'super'];

    /**
     * Defines the table name where the users are stored.
     */
    const TABLE_NAME = 'users';

    /**
     * List of validations for this model.
     */
    const VALIDATIONS = [
        'firstName' => [
            'presence'
        ],
        'lastName' => [
            'presence'
        ],
        'email' => [
            'presence',
            'email',
            'uniqueness' => [
                'table' => self::TABLE_NAME,
                'field' => 'email'
            ]
        ],
        'role' => [
            'presence',
            'inclusion' => self::ROLES
        ]
    ];

    /**
     * @var string First name of this user.
     */
    public $firstName;

    /**
     * @var string Last name of this user.
     */
    public $lastName;

    /**
     * @var string Email address for this user.
     */
    public $email;

    /**
     * @var string Encrypted password for this user to request a session.
     */
    public $password;

    /**
     * @var string Assigned role for this user from the ROLES constant.
     * @see ROLES
     */
    public $role;

}
