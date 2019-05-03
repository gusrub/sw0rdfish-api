<?php

namespace Sw0rdfish\Errors;

/**
 * An error that will be raised when trying to reach secured endpoints without
 * the proper role.
 */
class AuthorizationError extends GenericError
{
    /**
     * Defines the HTTP status code that will be exposed in the response for
     * this error
     */
    const HTTP_CODE = 403;
}

