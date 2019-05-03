<?php

namespace Sw0rdfish\Errors;

/**
 * A middleware to be used as a catch-all error handler for all requests.
 */
class Handler
{

    /**
     * Calls the middleware to catch errors with the given parameters.
     *
     * @param ServerRequestInterface $request The incoming request object.
     * @param ResponseInterface $response The response object to be returned.
     * @param \Exception $exception The throwable exception that caused this
     *  middleware to be called.
     * @return ResponseInterface The response object with the error details
     *  injected.
     */
    function __invoke($request, $response, $exception)
    {
        $errorClass = get_class($exception);
        $status = null;

        if (is_a($exception, "\Sw0rdfish\Errors\GenericError")) {
            $status = $errorClass::HTTP_CODE; 
        }
        else {
            $status = 500;
        }

        if ($status >= 500) {
            # log the error
            # TODO: Implement logging
            $error = [
                'message' => $exception->getMessage(),
                'logId' => null
            ];
        } else {
            $error = [
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors()
            ];
        }

        return $response->withJson($error)->withStatus($status);
    }
}
