<?php

namespace Sw0rdfish\Models;

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
use Sw0rdfish\Models\ModelException as ModelException;

/**
 * Represents base information that all secrets inherit.
 */
class Secret extends BaseModel
{

    /**
     * Defines the base table name where the secret information is stored.
     */
    const TABLE_NAME = 'secrets';

    /**
     * List of validations for this model.
     */
    const VALIDATIONS = [
        'name' => [
            'presence',
            'uniqueness' => [
                'table' => self::TABLE_NAME,
                'scope' => 'userId'
            ]
        ],
        'category' => [
            'presence',
            'inclusion' => [
                'bank_account_secret',
                'credit_card_secret',
                'email_secret',
                'generic_secret',
                'website_credential_secret'
            ]
        ],
        'userId' => [
            'presence',
            'numeric' => [
                'greaterThan' => 0
            ]
        ]
    ];

    /**
     * @var string A unique name _(per user)_ for this secret.
     */
    public $name;

    /**
     * @var string A brief description for this secret.
     */
    public $description;

    /**
     * @var string Any additional notes for this secret.
     */
    public $notes;

    /**
     * @var string The category for this secret.
     */
    public $category;

    /**
     * @var string The username for this secret. This will be encrypted.
     */
    public $username;

    /**
     * @var string The password for this secret. This will be encrypted.
     */
    public $password;

    /**
     * @var string The email for this secret.
     */
    public $email;

    /**
     * @var string The website URL for this secret.
     */
    public $website;

    /**
     * @var int The User ID that this secret belongs to
     */
    public $userId;

}
