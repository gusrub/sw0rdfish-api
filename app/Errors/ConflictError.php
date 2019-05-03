<?php

namespace Sw0rdfish\Errors;

/**
 * An error representing conflicts between resources.
 */
class ConflictError extends GenericError
{
    /**
     * Defines the HTTP status code that will be exposed in the response for
     * this error.
     */
    const HTTP_CODE = 409;
}

