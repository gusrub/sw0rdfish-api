<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

/**
 * Represents an exception that is thrown when a model fails to validate.
 */
class ValidationException extends \Exception
{
    /**
     * @var Array An array of error messages for this validation.
     */
    public $errors = [];

    /**
     * Creates a new instance of this exception.
     *
     * @param string $message The message of the exception.
     * @param Array $errors An array of validation error messages.
     */
    public function __construct($message, $errors)
    {
        $this->errors = $errors;
        parent::__construct($message, 0, null);
    }
}?>
