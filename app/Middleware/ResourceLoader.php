<?php

namespace Sw0rdfish\Middleware;

class ResourceLoader 
{
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
