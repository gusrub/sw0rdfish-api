<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\Secret as Secret;

/**
* A secret that stores credit card information
*/
class CreditCardSecret extends Secret
{

    /**
     * Defines the table name where the credit card information is stored.
     */
    const TABLE_NAME = 'credit_card_secrets';

    /**
     * Defines the base table name where the credit card information is
     * stored.
     */
    const BASE_TABLE_NAME = parent::TABLE_NAME;

    /**
     * List of validations for this model.
     */
    const VALIDATIONS = [
        'cardholder' => [
            'presence'
        ],
        'number' => [
            'presence',
            'numeric'
        ],
        'expirationYear' => [
            'presence',
            'numeric'
        ],
        'expirationMonth' => [
            'presence',
            'numeric'
        ],
        'csc' => [
            'presence',
            'numeric'
        ],
    ];

    /**
     * @var string The name as it appears on the credit card.
     */
    public $cardholder;

    /**
     * @var string 15 or 16 digits for this credit card.
     */
    public $number;

    /**
     * @var string The expiration year for this credit card _(e.g. 2010)_.
     */
    public $expirationYear;

    /**
     * @var string The expiration month for this credit card _(e.g. 11)_.
     */
    public $expirationMonth;

    /**
     * @var string The card security code _(usually 3 or 4 digits)_ of the back.
     */
    public $csc;

}
