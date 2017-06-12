<?php

namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;

/**
*
*/
class Secret extends BaseModel
{

    const TABLE_NAME = 'secrets';

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
