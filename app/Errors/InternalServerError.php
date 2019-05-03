<?php

namespace Sw0rdfish\Errors;

/**
 * Represents an unhandled error on the system that was not caused by the user
 * but for a malfunction within the system itself.
 */
class InternalServerError extends GenericError
{

    /**
     * Defines the HTTP status code that will be exposed in the response for
     * this error.
     */
    const HTTP_CODE = 500;
}
