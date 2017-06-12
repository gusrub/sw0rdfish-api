<?php

namespace Sw0rdfish\Models;

class ModelException extends \Exception
{
    public function __construct($message, $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}

