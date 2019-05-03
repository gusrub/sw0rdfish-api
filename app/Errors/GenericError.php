<?php

namespace Sw0rdfish\Errors;

/**
 * A generic error class where all other errors are based off of. This is also
 * the error that will be considered a system error if no more specific error
 * is raised.
 */
class GenericError extends \Exception
{
    /**
     * Defines the HTTP status code that will be exposed in the response for
     * this error.
     */
    const HTTP_CODE = 500;

    /**
     * @var Array An array of more specific details about this error.
     */
    protected $errors;

    /**
     * Creates a new instance of an error object.
     *
     * @param String $message The base message or a brief description of this
     *  error.
     * @param Array $errors An optional, extra array of errors in string format
     *  to give more details.
     */
    function __construct($message, Array $errors = null)
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    /**
     * Gets the detailed messages for this error.
     *
     * @return Array The list of error details.
     */
    function getErrors()
    {
        return $this->errors;
    }
}
