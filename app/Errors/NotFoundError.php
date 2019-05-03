<?php

namespace Sw0rdfish\Errors;

/**
 * Represents an error for a resource that does not exist with the given 
 * identifiers.
 */
class NotFoundError extends GenericError
{
    
    /**
     * Defines the HTTP status code that will be exposed in the response for
     * this error.
     */    
    const HTTP_CODE = 404;
}

