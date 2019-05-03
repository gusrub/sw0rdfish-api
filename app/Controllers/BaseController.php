<?php

namespace Sw0rdfish\Controllers;

/**
 * Represents a base abstract controller that can be used among the others to share
 * common functionality.
 */
abstract class BaseController
{
    /**
     * @var \Slim\Container A slim container interface object to hold
     * dependencies.
     */
    protected $container;

    /**
     * Creates a new instance of a BaseController.
     *
     * @param \Slim\Container $container A Slim container to manage
     * dependencies that may be injected.
     * @return \Sw0rdfish\Controllers\BaseController A new instance of a base
     * controller.
     */
    function __construct($container)
    {
        $this->container = $container;
    }

    // TODO: implement this in each controller inherited
    // abstract protected function sanitizedInput($request);
    // abstract protected function sanitizedOutput($object);
}