<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\Secret as Secret;

/**
* A secret that stores generic data
*/
class GenericSecret extends Secret
{
    /**
     * Defines the table name where the generic secrets information is stored.
     */
    const TABLE_NAME = 'generic_secrets';

    /**
     * Defines the base table name where the generic secret information is
     * stored.
     */
    const BASE_TABLE_NAME = parent::TABLE_NAME;

    /**
     * List of validations for this model.
     */
    const VALIDATIONS = [
        'secret' => [
            'presence'
        ]
    ];

    /**
     * @var string The generic text to store as a secret
     */
    public $secret;
}
