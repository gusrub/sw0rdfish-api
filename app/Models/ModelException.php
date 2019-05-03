<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

/**
 * Represents an exception that is thrown when a model fails.
 */
class ModelException extends \Exception
{
    /**
     * Creates a new instance of this exception.
     *
     * @param String $message The message of the exception.
     * @param \Exception $previous The parent exception, if any.
     */
    public function __construct($message, $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}

