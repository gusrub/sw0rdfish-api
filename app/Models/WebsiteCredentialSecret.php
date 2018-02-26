<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\Secret as Secret;

/**
* A secret that stores website credentials info.
*/
class WebsiteCredentialSecret extends Secret
{
    /**
     * Defines the table name where the website credential secrets information is stored.
     */
    const TABLE_NAME = 'website_credential_secrets';

    /**
     * Defines the base table name where the generic secret information is
     * stored.
     */
    const BASE_TABLE_NAME = parent::TABLE_NAME;

    /**
     * List of validations for this model.
     */
    const VALIDATIONS = [
        'url' => [
            'presence'
        ],
        'username' => [
            'presence'
        ],
        'password' => [
            'presence'
        ]
    ];

    /**
     * @var string The URL for the site.
     */
    public $url;

    /**
     * @var string The username for this site.
     */
    public $username;

    /**
     * @var string The password for this site.
     */
    public $password;
}
