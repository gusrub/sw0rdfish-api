<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\Secret as Secret;

/**
* A secret that stores bank account information
*/
class BankAccountSecret extends Secret
{

    /**
     * Defines the table name where the bank account information is stored.
     */
    const TABLE_NAME = 'bank_account_secrets';

    /**
     * Defines the base table name where the generic secret information is
     * stored.
     */
    const BASE_TABLE_NAME = parent::TABLE_NAME;

    /**
     * List of validations for this model.
     */
    const VALIDATIONS = [
        'accountNumber' => [
            'presence'
        ]
    ];

    /**
     * @var string The account number for this bank account.
     */
    public $accountNumber;

    /**
     * @var string The routing number for this bank account.
     */
    public $routingNumber;
}
