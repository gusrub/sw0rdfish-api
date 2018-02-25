<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\Secret as Secret;

/**
* A secret that stores email information
*/
class EmailSecret extends Secret
{
    /**
     * Defines the table name where the email secrets information is stored.
     */
    const TABLE_NAME = 'email_secrets';

    /**
     * Defines the base table name where the generic secret information is
     * stored.
     */
    const BASE_TABLE_NAME = parent::TABLE_NAME;

    /**
     * List of validations for this model.
     */
    const VALIDATIONS = [
        'email' => [
            'presence',
            'email'
        ],
        'password' => [
            'presence'
        ]
    ];

    /**
     * @var string The email address to store.
     */
    public $email;

    /**
     * @var string The password for this email address.
     */
    public $password;

    /**
     * @var string The URL for this email service provider.
     */
    public $url;
}
