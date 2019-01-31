<?php

namespace Sw0rdfish\Services;

/**
 * 
 */
class ResourceLoaderService
{
    public $type;
    public $id;
    public $resource;

    function __construct($type)
    {
        $this->type = "\\Sw0rdfish\\Models\\$type";
    }

    function load($id)
    {
        $this->id = $id;
        $this->resource = call_user_func($this->type . '::get', $id);
    }
}
