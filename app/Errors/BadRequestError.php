<?php

namespace Sw0rdfish\Errors;

/**
 * A generic error that represents a bad client request.
 */
class BadRequestError extends GenericError
{
    /**
     * Defines the HTTP status code that will be exposed in the response for
     * this error
     */
    const HTTP_CODE = 400;
}

