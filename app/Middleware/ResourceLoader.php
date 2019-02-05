<?php

namespace Sw0rdfish\Middleware;

/**
* This class allows for any type of resource to be loaded and injected in the
* request and is used as a middleware so we can load up nested resources in
* other resources like `users/1/tokens` where we would want to load the user
* with ID 1 in this case.
*/
class ResourceLoader
{
    /**
     * Loads a database model as a resource for the given type and identifier
     * and injects it in the given request. This method can be used as
     * middleware to preload nested resources.
     *
     * @param String $klass The relative model name that we want to load.
     * @param String $identifier The ID of the record that we want to load.
     * @param ServerRequestInterface $request A server request object where the
     *  resource will be loaded into.
     * @param ResponseInterface $response A server response object that will be
     *  used to handle the request.
     *
     * @return ServerRequestInterface A request object with the injected model
     *  instance.
     */
    public static function load($klass, $identifier, $request, $response)
    {
        $type = "\\Sw0rdfish\\Models\\$klass";
        $route = $request->getAttribute('route');
        $resourceId = $route->getArgument($identifier);
        $resource = call_user_func("$type::get", $resourceId);

        if(empty($resource)) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        $request = $request->withAttribute(lcfirst($klass), $resource);

        return $request;
    }
}
