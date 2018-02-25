<?php

namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;

/**
*
*/
class Secret extends BaseModel
{

    const TABLE_NAME = 'secrets';
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

    public $name;
    public $description;
    public $notes;
    public $category;
    public $userId;

    public static function categoryName()
    {
        return self::getShortName();
    }

}
