<?php

namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\Secret as Secret;

/**
*
*/
class CreditCardSecret extends Secret
{

    const TABLE_NAME = 'credit_card_secrets';
    const BASE_TABLE_NAME = parent::TABLE_NAME;
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

    public $cardholder;
    public $number;
    public $expirationYear;
    public $expirationMonth;
    public $csc;

}
